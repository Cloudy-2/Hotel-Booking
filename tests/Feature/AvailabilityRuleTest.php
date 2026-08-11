<?php

namespace Tests\Feature;

use App\Models\AvailabilityRule;
use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_outside_open_hours_is_rejected(): void
    {
        Carbon::setTestNow('2026-08-11 10:00:00');
        AvailabilityRule::create(['weekday' => 3, 'opens_at' => '10:00:00', 'closes_at' => '18:00:00']);
        $service = Service::create(['name' => 'Evening Suite', 'duration_minutes' => 60, 'price_cents' => 20000, 'is_active' => true]);

        $this->from('/reserve')->post('/bookings', [
            'service_id' => $service->id,
            'customer_name' => 'Late Guest',
            'customer_email' => 'late@example.com',
            'starts_at' => '2026-08-12 19:00:00',
        ])->assertRedirect('/reserve')->assertSessionHasErrors('starts_at');
    }

    public function test_booking_on_closed_holiday_is_rejected(): void
    {
        Carbon::setTestNow('2026-08-11 10:00:00');
        AvailabilityRule::create(['weekday' => 5, 'opens_at' => '08:00:00', 'closes_at' => '22:00:00']);
        Holiday::create(['date' => '2026-08-14', 'name' => 'Maintenance Day', 'is_closed' => true]);
        $service = Service::create(['name' => 'Holiday Suite', 'duration_minutes' => 60, 'price_cents' => 20000, 'is_active' => true]);

        $this->from('/reserve')->post('/bookings', [
            'service_id' => $service->id,
            'customer_name' => 'Holiday Guest',
            'customer_email' => 'holiday@example.com',
            'starts_at' => '2026-08-14 12:00:00',
        ])->assertRedirect('/reserve')->assertSessionHasErrors('starts_at');
    }

    public function test_availability_endpoint_returns_available_room_slots(): void
    {
        AvailabilityRule::create(['weekday' => 3, 'opens_at' => '08:00:00', 'closes_at' => '10:00:00']);
        $service = Service::create(['name' => 'Morning Room', 'duration_minutes' => 60, 'price_cents' => 15000, 'is_active' => true]);

        Booking::create([
            'service_id' => $service->id,
            'customer_name' => 'Booked Guest',
            'customer_email' => 'booked@example.com',
            'starts_at' => '2026-08-12 08:00:00',
            'ends_at' => '2026-08-12 09:00:00',
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $this->getJson('/availability?date=2026-08-12')
            ->assertOk()
            ->assertJsonPath('rooms.0.name', 'Morning Room')
            ->assertJsonPath('rooms.0.slots.0.label', '9:00 AM');
    }
}
