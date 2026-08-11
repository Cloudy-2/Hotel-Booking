@extends('layouts.app', [
    'title' => 'Edit Room | Aurelia Hotel',
    'section' => 'admin',
    'description' => 'Edit a room experience for Aurelia Hotel.',
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
                    <p class="section-kicker">Edit room</p>
                    <h1 class="page-title mt-3">{{ $service->name }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Update the guest-facing room listing, image showcase, amenities, and booking settings.</p>
                </div>
                <a class="btn btn-secondary" href="{{ route('services.index') }}">Back to Rooms</a>
            </div>

            <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data" data-service-form>
                @method('PUT')
                @include('services.form', ['buttonLabel' => 'Save Room'])
            </form>
        </section>
    </div>
@endsection
