<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome', [
    'services' => Service::where('is_active', true)->orderBy('price_cents')->get(),
]))->name('home');

Route::get('/admin', DashboardController::class)->name('dashboard');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
