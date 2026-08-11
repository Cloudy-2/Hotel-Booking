<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function create()
    {
        return view('bookings.create', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:180'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'starts_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $startsAt = Carbon::parse($validated['starts_at'])->seconds(0);
        $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

        if (Booking::overlapping($service->id, $startsAt->toDateTimeString(), $endsAt->toDateTimeString())->exists()) {
            return back()
                ->withErrors(['starts_at' => 'That time is already booked for the selected service.'])
                ->withInput();
        }

        Booking::create([
            ...$validated,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => Booking::STATUS_PENDING,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Booking request created. Review it from the dashboard.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED])],
        ]);

        $booking->update($validated);

        return back()->with('status', 'Booking status updated.');
    }
}
