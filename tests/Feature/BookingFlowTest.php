<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\AvailabilityRule;
use App\Models\Service;
use App\Models\User;
use App\Notifications\BookingRequestReceived;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_dashboard_render_services(): void
    {
        $admin = User::factory()->admin()->create();

        Service::create([
            'name' => 'Planning Session',
            'duration_minutes' => 45,
            'price_cents' => 5000,
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Curated room experiences')
            ->assertSee('Planning Session');

        $this->actingAs($admin)->get('/admin/reservations')
            ->assertOk()
            ->assertSee('Booking Dashboard')
            ->assertSee('Planning Session');

        $this->get('/admin')->assertRedirect('/admin/reservations');
        $this->get('/bookings/create')->assertRedirect('/reserve');
    }

    public function test_customer_can_create_booking(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-08-11 10:00:00');
        User::factory()->admin()->create();
        AvailabilityRule::create(['weekday' => 3, 'opens_at' => '08:00:00', 'closes_at' => '22:00:00']);

        $service = Service::create([
            'name' => 'Launch Support',
            'duration_minutes' => 60,
            'price_cents' => 10000,
            'is_active' => true,
        ]);

        $response = $this->post('/bookings', [
            'service_id' => $service->id,
            'customer_name' => 'Avery Stone',
            'customer_email' => 'avery@example.com',
            'customer_phone' => '555-0100',
            'starts_at' => '2026-08-12 09:30:00',
            'notes' => 'Please confirm by email.',
        ]);

        $response->assertRedirect(route('home'));

        $booking = Booking::first();

        $this->assertSame('Avery Stone', $booking->customer_name);
        $this->assertSame('avery@example.com', $booking->customer_email);
        $this->assertSame(Booking::STATUS_PENDING, $booking->status);
        $this->assertSame('2026-08-12 10:30:00', $booking->ends_at->format('Y-m-d H:i:s'));
        Notification::assertSentOnDemand(BookingRequestReceived::class);
    }

    public function test_overlapping_booking_is_rejected_for_same_service(): void
    {
        Carbon::setTestNow('2026-08-11 10:00:00');
        AvailabilityRule::create(['weekday' => 3, 'opens_at' => '08:00:00', 'closes_at' => '22:00:00']);

        $service = Service::create([
            'name' => 'Implementation Review',
            'duration_minutes' => 60,
            'price_cents' => 12000,
            'is_active' => true,
        ]);

        Booking::create([
            'service_id' => $service->id,
            'customer_name' => 'Existing Customer',
            'customer_email' => 'existing@example.com',
            'starts_at' => '2026-08-12 09:00:00',
            'ends_at' => '2026-08-12 10:00:00',
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $response = $this->from('/reserve')->post('/bookings', [
            'service_id' => $service->id,
            'customer_name' => 'New Customer',
            'customer_email' => 'new@example.com',
            'starts_at' => '2026-08-12 09:30:00',
        ]);

        $response
            ->assertRedirect('/reserve')
            ->assertSessionHasErrors('starts_at');

        $this->assertDatabaseMissing('bookings', [
            'customer_email' => 'new@example.com',
        ]);
    }
}
