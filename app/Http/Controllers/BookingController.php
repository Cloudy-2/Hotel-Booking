<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Notifications\BookingRequestReceived;
use App\Notifications\BookingStatusChanged;
use App\Support\Availability;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function create()
    {
        return view('bookings.create', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Availability $availability): RedirectResponse
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

        if ($message = $availability->validate($startsAt, $endsAt)) {
            return back()
                ->withErrors(['starts_at' => $message])
                ->withInput();
        }

        if (Booking::overlapping($service->id, $startsAt->toDateTimeString(), $endsAt->toDateTimeString())->exists()) {
            return back()
                ->withErrors(['starts_at' => 'That time is already booked for the selected service.'])
                ->withInput();
        }

        $booking = Booking::create([
            ...$validated,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => Booking::STATUS_PENDING,
        ]);

        Notification::route('mail', $booking->customer_email)->notify(new BookingRequestReceived($booking));
        User::where('role', User::ROLE_ADMIN)->each(fn (User $admin) => $admin->notify(new BookingRequestReceived($booking)));

        return redirect()
            ->route('home')
            ->with('status', 'Booking request created. The reservations team will review it soon.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED])],
        ]);

        $booking->update($validated);
        Notification::route('mail', $booking->customer_email)->notify(new BookingStatusChanged($booking));

        return back()->with('status', 'Booking status updated.');
    }
}
