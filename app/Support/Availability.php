<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\AvailabilityRule;
use App\Models\Holiday;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Availability
{
    public function validate(Carbon $startsAt, Carbon $endsAt): ?string
    {
        $holiday = Holiday::whereDate('date', $startsAt->toDateString())
            ->where('is_closed', true)
            ->first();

        if ($holiday) {
            return "Aurelia Hotel is closed for {$holiday->name}.";
        }

        $rule = AvailabilityRule::where('weekday', $startsAt->dayOfWeek)->first();

        if (! $rule || $rule->is_closed) {
            return 'The selected day is outside reservation hours.';
        }

        $opensAt = Carbon::parse($startsAt->toDateString().' '.$rule->opens_at);
        $closesAt = Carbon::parse($startsAt->toDateString().' '.$rule->closes_at);

        if ($startsAt->lt($opensAt) || $endsAt->gt($closesAt)) {
            return 'The selected time is outside reservation hours.';
        }

        return null;
    }

    public function roomsForDate(string $date): array
    {
        $day = Carbon::parse($date)->startOfDay();
        $holiday = Holiday::whereDate('date', $day->toDateString())
            ->where('is_closed', true)
            ->first();

        $rule = AvailabilityRule::where('weekday', $day->dayOfWeek)->first();

        if ($holiday || ! $rule || $rule->is_closed) {
            return [
                'date' => $day->toDateString(),
                'is_open' => false,
                'message' => $holiday
                    ? "Closed for {$holiday->name}."
                    : 'No reservation hours are configured for this date.',
                'rooms' => Service::where('is_active', true)->orderBy('price_cents')->get()->map(fn (Service $service) => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'image_url' => $service->image_url,
                    'gallery' => $service->gallery,
                    'amenities' => $service->amenities,
                    'max_guests' => $service->max_guests,
                    'room_size' => $service->room_size,
                    'price' => $service->formatted_price,
                    'duration_minutes' => $service->duration_minutes,
                    'slots' => [],
                ])->values()->all(),
            ];
        }

        $open = Carbon::parse($day->toDateString().' '.$rule->opens_at);
        $close = Carbon::parse($day->toDateString().' '.$rule->closes_at);
        $bookings = Booking::active()
            ->whereDate('starts_at', $day->toDateString())
            ->get()
            ->groupBy('service_id');

        return [
            'date' => $day->toDateString(),
            'is_open' => true,
            'message' => 'Showing available arrival times for '.$day->format('M j, Y').'.',
            'rooms' => Service::where('is_active', true)
                ->orderBy('price_cents')
                ->get()
                ->map(fn (Service $service) => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'image_url' => $service->image_url,
                    'gallery' => $service->gallery,
                    'amenities' => $service->amenities,
                    'max_guests' => $service->max_guests,
                    'room_size' => $service->room_size,
                    'price' => $service->formatted_price,
                    'duration_minutes' => $service->duration_minutes,
                    'slots' => $this->slotsForService($service, $open, $close, $bookings->get($service->id, collect())),
                ])
                ->values()
                ->all(),
        ];
    }

    private function slotsForService(Service $service, Carbon $open, Carbon $close, Collection $bookings): array
    {
        $slots = [];
        $cursor = $open->copy();

        while ($cursor->copy()->addMinutes($service->duration_minutes)->lte($close)) {
            $slotEnd = $cursor->copy()->addMinutes($service->duration_minutes);
            $hasConflict = $bookings->contains(fn (Booking $booking) => $booking->starts_at->lt($slotEnd) && $booking->ends_at->gt($cursor));

            if (! $hasConflict) {
                $slots[] = [
                    'label' => $cursor->format('g:i A'),
                    'value' => $cursor->format('Y-m-d\TH:i'),
                ];
            }

            $cursor->addMinutes(30);
        }

        return $slots;
    }
}
