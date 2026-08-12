@extends('layouts.app', [
    'title' => 'Admin Dashboard | Aurelia Hotel',
    'section' => 'admin',
    'description' => 'Manage Aurelia Hotel reservation requests, room experiences, and booking status updates.',
    'canonicalUrl' => route('dashboard'),
])

@section('content')
    @php
        $activeFilterCount = ($filters['status'] !== 'all' ? 1 : 0) + ($filters['date'] ? 1 : 0);
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="mb-5 px-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Management</p>
                <h1 class="mt-2 text-xl font-semibold text-stone-950">Reservation desk</h1>
            </div>
            <nav class="grid gap-1" aria-label="Admin navigation">
                <a class="admin-nav-link admin-nav-link-active" href="{{ route('dashboard') }}">
                    Dashboard
                    <span aria-hidden="true">01</span>
                </a>
                <a class="admin-nav-link" href="{{ route('services.index') }}">
                    Rooms
                    <span aria-hidden="true">02</span>
                </a>
                <a class="admin-nav-link" href="{{ route('availability.edit') }}">
                    Availability
                    <span aria-hidden="true">03</span>
                </a>
                <a class="admin-nav-link" href="{{ route('calendar.show') }}">
                    Calendar
                    <span aria-hidden="true">04</span>
                </a>
            </nav>
        </aside>

        <section class="min-w-0 p-4 sm:p-6 lg:p-8">
            <div class="dashboard-hero mb-6">
                <div>
                    <p class="section-kicker">Operations</p>
                    <h2 class="page-title mt-3">Booking Dashboard</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Review requests, approve arrivals, and keep room operations moving from one focused workspace.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a class="btn btn-secondary" href="{{ route('services.index') }}">Manage Rooms</a>
                    <a class="btn btn-primary" href="{{ route('bookings.create') }}">Create Booking</a>
                </div>
            </div>

            <section class="dashboard-stats" aria-label="Booking statistics">
                <a class="dashboard-stat-card dashboard-stat-card-primary" href="{{ route('dashboard', ['status' => 'pending']) }}">
                    <span class="stat-label">Pending review</span>
                    <strong class="stat-value">{{ $stats['pending'] }}</strong>
                    <p>Requests waiting for a team decision.</p>
                </a>
                <a class="dashboard-stat-card" href="{{ route('dashboard', ['status' => 'confirmed']) }}">
                    <span class="stat-label">Confirmed stays</span>
                    <strong class="stat-value">{{ $stats['confirmed'] }}</strong>
                    <p>Approved reservations on the books.</p>
                </a>
                <a class="dashboard-stat-card" href="{{ route('dashboard', ['date' => now()->toDateString()]) }}">
                    <span class="stat-label">Today</span>
                    <strong class="stat-value">{{ $stats['today'] }}</strong>
                    <p>Arrivals scheduled for today.</p>
                </a>
                <a class="dashboard-stat-card" href="{{ route('services.index') }}">
                    <span class="stat-label">Active rooms</span>
                    <strong class="stat-value">{{ $stats['services'] }}</strong>
                    <p>Room experiences available to guests.</p>
                </a>
                <a class="dashboard-stat-card" href="{{ route('dashboard', ['status' => 'cancelled']) }}">
                    <span class="stat-label">Cancelled</span>
                    <strong class="stat-value">{{ $stats['cancelled'] }}</strong>
                    <p>Closed requests kept for reference.</p>
                </a>
            </section>

            <section class="reservation-desk mt-6">
                <div class="reservation-toolbar">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Reservations</h2>
                        <p class="mt-2 text-sm text-stone-600">{{ $bookings->count() }} {{ \Illuminate\Support\Str::plural('booking', $bookings->count()) }} shown{{ $activeFilterCount ? ' with ' . $activeFilterCount . ' active ' . \Illuminate\Support\Str::plural('filter', $activeFilterCount) : '' }}.</p>
                    </div>
                    <form class="reservation-filter-form" method="GET" action="{{ route('dashboard') }}">
                        <label class="field-label">
                            Status
                            <select class="field-control" name="status">
                                <option value="all" @selected($filters['status'] === 'all')>All</option>
                                <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                                <option value="confirmed" @selected($filters['status'] === 'confirmed')>Confirmed</option>
                                <option value="cancelled" @selected($filters['status'] === 'cancelled')>Cancelled</option>
                            </select>
                        </label>
                        <label class="field-label">
                            Arrival date
                            <input class="field-control" type="date" name="date" value="{{ $filters['date'] }}">
                        </label>
                        <div class="flex items-end">
                            <button class="btn btn-secondary w-full" type="submit">Filter</button>
                        </div>
                        @if ($activeFilterCount)
                            <div class="flex items-end">
                                <a class="btn btn-ghost w-full" href="{{ route('dashboard') }}">Clear</a>
                            </div>
                        @endif
                    </form>
                </div>

                <div class="reservation-quick-filters" aria-label="Quick reservation filters">
                    <a class="{{ $filters['status'] === 'all' && !$filters['date'] ? 'is-active' : '' }}" href="{{ route('dashboard') }}">All</a>
                    <a class="{{ $filters['status'] === 'pending' ? 'is-active' : '' }}" href="{{ route('dashboard', ['status' => 'pending']) }}">Needs review</a>
                    <a class="{{ $filters['status'] === 'confirmed' ? 'is-active' : '' }}" href="{{ route('dashboard', ['status' => 'confirmed']) }}">Confirmed</a>
                    <a class="{{ $filters['date'] === now()->toDateString() ? 'is-active' : '' }}" href="{{ route('dashboard', ['date' => now()->toDateString()]) }}">Today</a>
                    <a class="{{ $filters['status'] === 'cancelled' ? 'is-active' : '' }}" href="{{ route('dashboard', ['status' => 'cancelled']) }}">Cancelled</a>
                </div>

                @if ($bookings->isEmpty())
                    <div class="reservation-empty-state">
                        <span>No matches</span>
                        <h3>No reservations found.</h3>
                        <p>Try another status or date. New guest requests will appear here as soon as they are submitted.</p>
                        <div class="mt-5 flex flex-wrap justify-center gap-2">
                            <a class="btn btn-primary" href="{{ route('bookings.create') }}">Create booking</a>
                            <a class="btn btn-secondary" href="{{ route('dashboard') }}">Reset filters</a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="data-table responsive-table reservation-table">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Arrival</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr class="reservation-row reservation-row-{{ $booking->status }}">
                                        <td data-label="Guest">
                                            <div class="reservation-guest">
                                                <span>{{ strtoupper(substr($booking->customer_name, 0, 1)) }}</span>
                                                <div>
                                                    <strong class="block font-semibold text-stone-950">{{ $booking->customer_name }}</strong>
                                                    <a class="text-stone-500 hover:text-stone-950" href="mailto:{{ $booking->customer_email }}">{{ $booking->customer_email }}</a>
                                                </div>
                                            </div>
                                            @if ($booking->customer_phone)
                                                <a class="mt-2 block text-stone-500 hover:text-stone-950" href="tel:{{ $booking->customer_phone }}">{{ $booking->customer_phone }}</a>
                                            @endif
                                        </td>
                                        <td data-label="Room">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $booking->service->image_url }}" alt="" class="size-14 rounded-md object-cover">
                                                <span>
                                                    <span class="font-medium text-stone-950">{{ $booking->service->name }}</span>
                                                    <span class="mt-1 block text-stone-500">{{ $booking->service->formatted_price }} · up to {{ $booking->service->max_guests ?? 2 }} guests</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td data-label="Arrival">
                                            <span class="reservation-date">{{ $booking->starts_at->format('M j') }}</span>
                                            <span class="font-medium text-stone-950">{{ $booking->starts_at->format('Y g:i A') }}</span>
                                            <span class="mt-1 block text-stone-500">Ends {{ $booking->ends_at->format('g:i A') }}</span>
                                        </td>
                                        <td data-label="Notes">
                                            <span class="line-clamp-2 text-stone-600">{{ $booking->notes ?: 'No guest notes' }}</span>
                                        </td>
                                        <td data-label="Status">
                                            <span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span>
                                        </td>
                                        <td data-label="Actions">
                                            <div class="flex flex-wrap justify-start gap-2 md:justify-end">
                                                @if ($booking->status === \App\Models\Booking::STATUS_PENDING)
                                                    <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button class="btn btn-secondary min-h-9 px-3 py-1.5" type="submit" data-loading-text="Confirming...">Confirm</button>
                                                    </form>
                                                @endif

                                                @if ($booking->status === \App\Models\Booking::STATUS_CANCELLED)
                                                    <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="pending">
                                                        <button class="btn btn-secondary min-h-9 px-3 py-1.5" type="submit" data-loading-text="Reopening...">Reopen</button>
                                                    </form>
                                                @endif

                                                @if ($booking->status !== \App\Models\Booking::STATUS_CANCELLED)
                                                    <form
                                                        method="POST"
                                                        action="{{ route('bookings.status', $booking) }}"
                                                        data-confirm-action="This will move {{ $booking->customer_name }}'s reservation to cancelled and notify the guest."
                                                        data-confirm-title="Cancel reservation?"
                                                        data-confirm-text="Yes, cancel it"
                                                        data-cancel-text="Keep reservation"
                                                    >
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button class="btn btn-danger min-h-9 px-3 py-1.5" type="submit" data-loading-text="Cancelling...">Cancel</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="mt-6 grid gap-4 lg:grid-cols-3" aria-label="Active rooms">
                @foreach ($services as $service)
                    <article class="room-summary-card">
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="font-semibold text-stone-950">{{ $service->name }}</h3>
                            <span class="text-sm font-semibold text-stone-950">{{ $service->formatted_price }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4 text-sm text-stone-500">
                            <span>{{ $service->duration_minutes }} min</span>
                            <span>Active</span>
                        </div>
                    </article>
                @endforeach
            </section>
        </section>
    </div>
@endsection
