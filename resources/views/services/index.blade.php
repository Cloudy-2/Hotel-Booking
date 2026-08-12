@extends('layouts.app', [
    'title' => 'Room Management | Aurelia Hotel',
    'section' => 'admin',
    'description' => 'Manage Aurelia Hotel room experiences and availability.',
])

@section('content')
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="mb-5 px-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Management</p>
                <h1 class="mt-2 text-xl font-semibold text-stone-950">Room inventory</h1>
            </div>
            <nav class="grid gap-1" aria-label="Admin navigation">
                <a class="admin-nav-link" href="{{ route('dashboard') }}">Reservations <span>01</span></a>
                <a class="admin-nav-link admin-nav-link-active" href="{{ route('services.index') }}">Rooms <span>02</span></a>
                <a class="admin-nav-link" href="{{ route('availability.edit') }}">Availability <span>03</span></a>
                <a class="admin-nav-link" href="{{ route('calendar.show') }}">Calendar <span>04</span></a>
            </nav>
        </aside>

        <section class="min-w-0 p-4 sm:p-6 lg:p-8">
            @php
                $activeCount = $services->where('is_active', true)->count();
                $disabledCount = $services->count() - $activeCount;
                $averageRate = $services->count() > 0 ? $services->avg('price_cents') / 100 : 0;
            @endphp

            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="section-kicker">Inventory</p>
                    <h1 class="page-title mt-3">Room Management</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Create, edit, and disable room experiences shown on the guest site.</p>
                </div>
                <a class="btn btn-primary" href="{{ route('services.create') }}">New Room</a>
            </div>

            <div class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="admin-metric">
                    <span class="admin-metric-label">Total rooms</span>
                    <strong>{{ $services->count() }}</strong>
                </div>
                <div class="admin-metric">
                    <span class="admin-metric-label">Active</span>
                    <strong>{{ $activeCount }}</strong>
                </div>
                <div class="admin-metric">
                    <span class="admin-metric-label">Disabled</span>
                    <strong>{{ $disabledCount }}</strong>
                </div>
                <div class="admin-metric">
                    <span class="admin-metric-label">Average rate</span>
                    <strong>${{ number_format($averageRate, 2) }}</strong>
                </div>
            </div>

            <section class="panel admin-inventory-panel overflow-hidden" data-room-management>
                <div class="panel-header gap-5">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Room list</h2>
                        <p class="mt-2 text-sm text-stone-600"><span data-room-visible-count>{{ $services->count() }}</span> visible of {{ $services->count() }} room experiences.</p>
                    </div>
                    <div class="grid w-full gap-3 lg:max-w-3xl lg:grid-cols-[1fr_auto_auto]">
                        <label class="admin-search">
                            <span class="sr-only">Search rooms</span>
                            <input type="search" placeholder="Search room, amenity, size..." data-room-search>
                        </label>
                        <label class="admin-filter">
                            <span class="sr-only">Filter status</span>
                            <select data-room-status-filter>
                                <option value="all">All status</option>
                                <option value="active">Active</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </label>
                        <label class="admin-filter">
                            <span class="sr-only">Sort rooms</span>
                            <select data-room-sort>
                                <option value="name">Sort by name</option>
                                <option value="rate_desc">Highest rate</option>
                                <option value="rate_asc">Lowest rate</option>
                                <option value="guests_desc">Most guests</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table room-list-table">
                        <thead>
                            <tr>
                                <th class="min-w-[360px]">Room</th>
                                <th>Rate</th>
                                <th>Guests</th>
                                <th>Size</th>
                                <th class="min-w-[260px]">Amenities</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                <tr
                                    data-room-row
                                    data-name="{{ strtolower($service->name) }}"
                                    data-search="{{ strtolower($service->name.' '.$service->description.' '.implode(' ', $service->amenities).' '.$service->room_size) }}"
                                    data-status="{{ $service->is_active ? 'active' : 'disabled' }}"
                                    data-rate="{{ $service->price_cents }}"
                                    data-guests="{{ $service->max_guests ?? 2 }}"
                                >
                                    <td data-label="Room">
                                        <div class="flex min-w-0 items-center gap-4">
                                            <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="h-20 w-28 shrink-0 rounded-md object-cover">
                                            <div class="min-w-0">
                                                <h3 class="font-semibold text-stone-950">{{ $service->name }}</h3>
                                                <p class="mt-1 line-clamp-2 max-w-xl text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                                                <span class="mt-2 inline-flex text-xs font-medium text-stone-500">{{ $service->duration_minutes }} min reservation window</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Rate">
                                        <span class="font-semibold text-stone-950">{{ $service->formatted_price }}</span>
                                    </td>
                                    <td data-label="Guests">Up to {{ $service->max_guests ?? 2 }}</td>
                                    <td data-label="Size">{{ $service->room_size ?? 'Not set' }}</td>
                                    <td data-label="Amenities">
                                        <div class="flex max-w-sm flex-wrap gap-2">
                                            @foreach (array_slice($service->amenities, 0, 4) as $amenity)
                                                <span class="rounded-full border border-stone-200 px-2.5 py-1 text-xs font-medium text-stone-600">{{ $amenity }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge {{ $service->is_active ? 'badge-confirmed' : 'badge-cancelled' }}">{{ $service->is_active ? 'Active' : 'Disabled' }}</span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="flex flex-nowrap justify-start gap-2 lg:justify-end">
                                            <a class="btn btn-secondary min-h-9 px-3 py-1.5" href="{{ route('services.edit', $service) }}">Edit</a>
                                            @if ($service->is_active)
                                                <form
                                                    method="POST"
                                                    action="{{ route('services.destroy', $service) }}"
                                                    data-confirm-action="This will hide {{ $service->name }} from the guest booking pages."
                                                    data-confirm-title="Disable room?"
                                                    data-confirm-text="Yes, disable it"
                                                    data-cancel-text="Keep active"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger min-h-9 px-3 py-1.5" type="submit" data-loading-text="Disabling...">Disable</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="hidden px-5 py-12 text-center" data-room-empty-state>
                    <h3 class="text-lg font-semibold text-stone-950">No rooms match your filters.</h3>
                    <p class="mt-2 text-sm text-stone-600">Try a different search term or show all statuses.</p>
                </div>
            </section>
        </section>
    </div>
@endsection
