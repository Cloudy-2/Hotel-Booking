<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\AvailabilityRule;
use App\Models\Service;
use App\Models\User;
use App\Notifications\BookingRequestReceived;
use App\Notifications\BookingStatusChanged;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_rooms_page_and_dashboard_render_services(): void
    {
        $admin = User::factory()->admin()->create();

        Service::create([
            'name' => 'Planning Session',
            'duration_minutes' => 45,
            'price_cents' => 5000,
            'is_active' => true,
        ]);

        $this->get('/rooms')
            ->assertOk()
            ->assertSee('Curated room experiences')
            ->assertSee('Planning Session');

        $this->get('/hotel')
            ->assertOk()
            ->assertSee('Welcome to our boutique hotel.');

        $this->get('/food-and-drink')
            ->assertOk()
            ->assertSee('Slow mornings, polished evenings.');

        $this->get('/contact')
            ->assertOk()
            ->assertSee('Ask us anything.');

        $this->post('/contact', [
            'first_name' => 'Avery',
            'last_name' => 'Stone',
            'email' => 'avery@example.com',
            'message' => 'Could you help with a late arrival?',
        ])->assertRedirect()
            ->assertSessionHas('status');

        $this->actingAs($admin)->get('/admin/reservations')
            ->assertOk()
            ->assertSee('Booking Dashboard')
            ->assertSee('Planning Session');

        $this->actingAs($admin)->get('/admin/calendar')
            ->assertOk()
            ->assertSee('Confirmed stays')
            ->assertSee('Export .ics');

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

    public function test_admin_can_update_booking_status_with_feedback(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();

        $service = Service::create([
            'name' => 'Workspace Consultation',
            'duration_minutes' => 45,
            'price_cents' => 7500,
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'service_id' => $service->id,
            'customer_name' => 'Avery Stone',
            'customer_email' => 'avery@example.com',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addMinutes(45),
            'status' => Booking::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('bookings.status', $booking), ['status' => Booking::STATUS_CONFIRMED])
            ->assertRedirect()
            ->assertSessionHas('feedback_title', 'Reservation confirmed');

        $this->assertSame(Booking::STATUS_CONFIRMED, $booking->fresh()->status);

        $this->actingAs($admin)
            ->patch(route('bookings.status', $booking), ['status' => Booking::STATUS_CANCELLED])
            ->assertRedirect()
            ->assertSessionHas('feedback_title', 'Reservation cancelled');

        $this->assertSame(Booking::STATUS_CANCELLED, $booking->fresh()->status);

        $this->actingAs($admin)
            ->patch(route('bookings.status', $booking), ['status' => Booking::STATUS_PENDING])
            ->assertRedirect()
            ->assertSessionHas('feedback_title', 'Reservation reopened');

        $this->assertSame(Booking::STATUS_PENDING, $booking->fresh()->status);
        Notification::assertSentOnDemand(BookingStatusChanged::class);
    }
}
