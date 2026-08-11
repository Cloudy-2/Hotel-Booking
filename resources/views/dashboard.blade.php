@extends('layouts.app', ['title' => 'Admin Dashboard | Aurelia Hotel', 'section' => 'admin'])

@section('content')
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
                <a class="admin-nav-link" href="{{ route('bookings.create') }}">
                    New booking
                    <span aria-hidden="true">02</span>
                </a>
                <a class="admin-nav-link" href="{{ route('home') }}">
                    Guest site
                    <span aria-hidden="true">03</span>
                </a>
            </nav>
        </aside>

        <section class="min-w-0 p-4 sm:p-6 lg:p-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="section-kicker">Operations</p>
                    <h2 class="page-title mt-3">Booking Dashboard</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Review upcoming requests, confirm reservations, and monitor active room experiences.</p>
                </div>
                <a class="btn btn-primary" href="{{ route('bookings.create') }}">Create Booking</a>
            </div>

            <section class="grid gap-4 md:grid-cols-3" aria-label="Booking statistics">
                <div class="stat-panel">
                    <span class="stat-label">Active rooms</span>
                    <strong class="stat-value">{{ $stats['services'] }}</strong>
                    <p class="mt-3 text-sm text-stone-500">Available to guests</p>
                </div>
                <div class="stat-panel">
                    <span class="stat-label">Pending review</span>
                    <strong class="stat-value">{{ $stats['pending'] }}</strong>
                    <p class="mt-3 text-sm text-stone-500">Need team action</p>
                </div>
                <div class="stat-panel">
                    <span class="stat-label">Confirmed stays</span>
                    <strong class="stat-value">{{ $stats['confirmed'] }}</strong>
                    <p class="mt-3 text-sm text-stone-500">Currently approved</p>
                </div>
            </section>

            <section class="panel mt-6 overflow-hidden">
                <div class="panel-header">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Reservations</h2>
                        <p class="mt-2 text-sm text-stone-600">Latest booking requests ordered by arrival time.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge badge-pending">Pending</span>
                        <span class="badge badge-confirmed">Confirmed</span>
                        <span class="badge badge-cancelled">Cancelled</span>
                    </div>
                </div>

                @if ($bookings->isEmpty())
                    <div class="empty-state">
                        <h3 class="text-lg font-semibold text-stone-950">No reservations yet</h3>
                        <p class="mt-2 text-sm text-stone-600">New guest requests will appear here once submitted.</p>
                        <a class="btn btn-secondary mt-5" href="{{ route('bookings.create') }}">Create the first booking</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="data-table responsive-table">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Arrival</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td data-label="Guest">
                                            <strong class="block font-semibold text-stone-950">{{ $booking->customer_name }}</strong>
                                            <span class="text-stone-500">{{ $booking->customer_email }}</span>
                                        </td>
                                        <td data-label="Room">
                                            <span class="font-medium text-stone-950">{{ $booking->service->name }}</span>
                                            <span class="mt-1 block text-stone-500">{{ $booking->service->formatted_price }}</span>
                                        </td>
                                        <td data-label="Arrival">
                                            <span class="font-medium text-stone-950">{{ $booking->starts_at->format('M j, Y g:i A') }}</span>
                                            <span class="mt-1 block text-stone-500">Ends {{ $booking->ends_at->format('g:i A') }}</span>
                                        </td>
                                        <td data-label="Status">
                                            <span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span>
                                        </td>
                                        <td data-label="Actions">
                                            @if ($booking->status !== \App\Models\Booking::STATUS_CANCELLED)
                                                <div class="flex flex-wrap justify-start gap-2 md:justify-end">
                                                    <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button class="btn btn-secondary min-h-9 px-3 py-1.5" type="submit">Confirm</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('bookings.status', $booking) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button class="btn btn-danger min-h-9 px-3 py-1.5" type="submit">Cancel</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="block text-right text-sm text-stone-500 md:text-right">Closed</span>
                                            @endif
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
                    <article class="panel p-5">
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
