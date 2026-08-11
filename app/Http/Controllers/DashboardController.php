<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
            'bookings' => Booking::with('service')->latest('starts_at')->limit(12)->get(),
            'stats' => [
                'services' => Service::where('is_active', true)->count(),
                'pending' => Booking::where('status', Booking::STATUS_PENDING)->count(),
                'confirmed' => Booking::where('status', Booking::STATUS_CONFIRMED)->count(),
            ],
        ]);
    }
}
