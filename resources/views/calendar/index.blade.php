@extends('layouts.app', [
    'title' => 'Booking Calendar | Aurelia Hotel',
    'section' => 'admin',
    'description' => 'View confirmed Aurelia Hotel bookings by arrival date.',
])

@section('content')
    @php
        $todayBookings = $bookings->filter(fn ($booking) => $booking->starts_at->isToday());
        $upcomingBookings = $bookings->filter(fn ($booking) => $booking->starts_at->isToday() || $booking->starts_at->isFuture());
        $nextBooking = $upcomingBookings->first();
        $groupedBookings = $bookings->groupBy(fn ($booking) => $booking->starts_at->format('Y-m-d'));
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="mb-5 px-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Management</p>
                <h1 class="mt-2 text-xl font-semibold text-stone-950">Operations</h1>
            </div>
            <nav class="grid gap-1" aria-label="Admin navigation">
                <a class="admin-nav-link" href="{{ route('dashboard') }}">Reservations <span>01</span></a>
                <a class="admin-nav-link" href="{{ route('services.index') }}">Rooms <span>02</span></a>
                <a class="admin-nav-link" href="{{ route('availability.edit') }}">Availability <span>03</span></a>
                <a class="admin-nav-link admin-nav-link-active" href="{{ route('calendar.show') }}">Calendar <span>04</span></a>
            </nav>
        </aside>

        <section class="min-w-0 p-4 sm:p-6 lg:p-8">
            <div class="calendar-hero mb-5">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="section-kicker">Calendar</p>
                        <h1 class="page-title mt-3">Booking calendar</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Track confirmed stays inside the admin workspace. Export the .ics file only when you need to add these arrivals to another calendar app.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a class="btn btn-secondary" href="{{ route('dashboard') }}">View reservations</a>
                        <a class="btn btn-primary" href="{{ route('calendar.export') }}">Export .ics</a>
                    </div>
                </div>
            </div>

            <div class="calendar-overview mb-5">
                <article class="calendar-focus-card">
                    <p class="section-kicker">Calendar</p>
                    <div class="mt-5 flex items-end gap-3">
                        <span class="calendar-date-tile">
                            <span>{{ now()->format('M') }}</span>
                            <strong>{{ now()->format('d') }}</strong>
                        </span>
                        <div>
                            <h2 class="text-2xl font-semibold text-white">Today</h2>
                            <p class="mt-1 text-sm text-stone-300">{{ $todayBookings->count() }} confirmed {{ Str::plural('arrival', $todayBookings->count()) }}</p>
                        </div>
                    </div>
                </article>
                <article class="calendar-stat-card">
                    <span class="admin-metric-label">Confirmed stays</span>
                    <strong>{{ $bookings->count() }}</strong>
                    <p>Approved reservations currently visible in this calendar.</p>
                </article>
                <article class="calendar-stat-card">
                    <span class="admin-metric-label">Upcoming</span>
                    <strong>{{ $upcomingBookings->count() }}</strong>
                    <p>Arrivals from today forward.</p>
                </article>
                <article class="calendar-stat-card">
                    <span class="admin-metric-label">Next arrival</span>
                    <strong class="text-lg">{{ $nextBooking ? $nextBooking->starts_at->format('M j, g:i A') : 'None set' }}</strong>
                    <p>{{ $nextBooking ? $nextBooking->customer_name . ' for ' . $nextBooking->service->name : 'Confirm a reservation to schedule the next stay.' }}</p>
                </article>
            </div>

            <section class="calendar-schedule-panel">
                <div class="panel-header">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Arrival schedule</h2>
                        <p class="mt-2 text-sm text-stone-600">Only bookings marked as confirmed appear here. Pending requests stay in Reservations until approved.</p>
                    </div>
                    <a class="calendar-export-link" href="{{ route('calendar.export') }}">Download calendar file</a>
                </div>

                @forelse ($groupedBookings as $date => $dateBookings)
                    <div class="calendar-date-group">
                        <div class="calendar-date-marker">
                            <span>{{ \Carbon\Carbon::parse($date)->format('M') }}</span>
                            <strong>{{ \Carbon\Carbon::parse($date)->format('d') }}</strong>
                            <small>{{ \Carbon\Carbon::parse($date)->format('D') }}</small>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                                <h3 class="text-base font-semibold text-stone-950">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h3>
                                <span class="text-sm font-medium text-stone-500">{{ $dateBookings->count() }} {{ Str::plural('stay', $dateBookings->count()) }}</span>
                            </div>

                            <div class="grid gap-3">
                                @foreach ($dateBookings as $booking)
                                    <article class="calendar-event-card">
                                        <div class="calendar-time-block">
                                            <span>{{ $booking->starts_at->format('g:i A') }}</span>
                                            <small>Until {{ $booking->ends_at->format('g:i A') }}</small>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                <h4 class="font-semibold text-stone-950">{{ $booking->service->name }}</h4>
                                                <span class="badge badge-confirmed self-start sm:self-auto">Confirmed</span>
                                            </div>
                                            <p class="mt-1 text-sm text-stone-600">{{ $booking->customer_name }} | {{ $booking->customer_email }}</p>
                                            @if ($booking->customer_phone)
                                                <p class="mt-1 text-sm text-stone-500">{{ $booking->customer_phone }}</p>
                                            @endif
                                            @if ($booking->notes)
                                                <p class="mt-3 rounded-md bg-stone-50 px-3 py-2 text-sm text-stone-600">{{ $booking->notes }}</p>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="calendar-empty-state">
                        <span class="calendar-empty-date">{{ now()->format('M d') }}</span>
                        <h3>No confirmed bookings yet.</h3>
                        <p>When a request is approved from Reservations, it will appear here by arrival date with guest details and room information.</p>
                        <div class="mt-5 flex flex-wrap justify-center gap-2">
                            <a class="btn btn-primary" href="{{ route('dashboard') }}">Review reservations</a>
                            <a class="btn btn-secondary" href="{{ route('calendar.export') }}">Export .ics</a>
                        </div>
                    </div>
                @endforelse
            </section>
        </section>
    </div>
@endsection
