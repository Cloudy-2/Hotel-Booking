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
                <a class="admin-nav-link" href="{{ route('calendar.export') }}">Calendar <span>03</span></a>
            </nav>
        </aside>

        <section class="min-w-0 p-4 sm:p-6 lg:p-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="section-kicker">Inventory</p>
                    <h1 class="page-title mt-3">Room Management</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Create, edit, and disable room experiences shown on the guest site.</p>
                </div>
                <a class="btn btn-primary" href="{{ route('services.create') }}">New Room</a>
            </div>

            <section class="panel overflow-hidden">
                <div class="panel-header">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Room list</h2>
                        <p class="mt-2 text-sm text-stone-600">{{ $services->count() }} room experiences in inventory.</p>
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
                                <tr>
                                    <td data-label="Room">
                                        <div class="flex min-w-0 items-center gap-4">
                                            <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="h-24 w-32 shrink-0 rounded-md object-cover">
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
                                        <div class="flex flex-wrap justify-start gap-2 lg:justify-end">
                                            <a class="btn btn-secondary min-h-9 px-3 py-1.5" href="{{ route('services.edit', $service) }}">Edit</a>
                                            @if ($service->is_active)
                                                <form method="POST" action="{{ route('services.destroy', $service) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger min-h-9 px-3 py-1.5" type="submit">Disable</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </div>
@endsection
