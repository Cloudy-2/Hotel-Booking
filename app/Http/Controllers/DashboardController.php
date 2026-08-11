<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $status = $request->query('status', 'all');
        $date = $request->query('date');

        $bookings = Booking::with('service')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($date, fn ($query) => $query->whereDate('starts_at', $date))
            ->orderBy('starts_at')
            ->limit(40)
            ->get();

        return view('dashboard', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
            'bookings' => $bookings,
            'filters' => [
                'status' => $status,
                'date' => $date,
            ],
            'stats' => [
                'services' => Service::where('is_active', true)->count(),
                'pending' => Booking::where('status', Booking::STATUS_PENDING)->count(),
                'confirmed' => Booking::where('status', Booking::STATUS_CONFIRMED)->count(),
                'cancelled' => Booking::where('status', Booking::STATUS_CANCELLED)->count(),
                'today' => Booking::whereDate('starts_at', today())->active()->count(),
            ],
        ]);
    }
}
