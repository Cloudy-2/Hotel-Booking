<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\AvailabilityCheckController;
use App\Http\Controllers\CalendarExportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::view('/hotel', 'hotel')->name('hotel.show');

Route::get('/rooms', fn () => view('rooms', [
    'services' => Service::where('is_active', true)->orderBy('price_cents')->get(),
]))->name('rooms.index');

Route::view('/food-and-drink', 'dining')->name('dining.index');

Route::view('/contact', 'contact')->name('contact.show');
Route::post('/contact', function (Request $request) {
    $request->validate([
        'first_name' => ['required', 'string', 'max:80'],
        'last_name' => ['required', 'string', 'max:80'],
        'email' => ['required', 'email', 'max:255'],
        'message' => ['required', 'string', 'max:600'],
    ]);

    return back()->with([
        'feedback_title' => 'Message received',
        'status' => 'Thank you. The Aurelia team will review your question and respond as soon as possible.',
    ]);
})->name('contact.store');

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
    Route::get('/admin/availability', [AvailabilityController::class, 'edit'])->name('availability.edit');
    Route::put('/admin/availability', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::post('/admin/holidays', [AvailabilityController::class, 'storeHoliday'])->name('holidays.store');
    Route::delete('/admin/holidays/{holiday}', [AvailabilityController::class, 'destroyHoliday'])->name('holidays.destroy');
    Route::get('/admin/calendar', fn () => view('calendar.index', [
        'bookings' => Booking::with('service')
            ->where('status', Booking::STATUS_CONFIRMED)
            ->orderBy('starts_at')
            ->get(),
    ]))->name('calendar.show');
    Route::get('/admin/calendar.ics', CalendarExportController::class)->name('calendar.export');
});
