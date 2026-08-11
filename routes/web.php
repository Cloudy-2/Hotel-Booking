<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityCheckController;
use App\Http\Controllers\CalendarExportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome', [
    'services' => Service::where('is_active', true)->orderBy('price_cents')->get(),
]))->name('home');

Route::redirect('/admin', '/admin/reservations');
Route::redirect('/bookings/create', '/reserve');
Route::get('/availability', AvailabilityCheckController::class)->name('availability.check');
Route::get('/reserve', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/reservations', DashboardController::class)->name('dashboard');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    Route::resource('/admin/services', ServiceController::class)->except('show');
    Route::get('/admin/calendar.ics', CalendarExportController::class)->name('calendar.export');
});
