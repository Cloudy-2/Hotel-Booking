@extends('layouts.app', [
    'title' => 'Reserve a Room | Aurelia Hotel',
    'section' => 'guest',
    'description' => 'Submit a room reservation request for Aurelia Hotel with clear guest details and booking summary.',
    'canonicalUrl' => route('bookings.create'),
])

@php
    $selectedServiceId = old('service_id', request('service_id'));
    $selectedStartsAt = old('starts_at', request('starts_at'));
    $selectedArrivalDate = now()->toDateString();
    $selectedArrivalValue = '';

    if ($selectedStartsAt) {
        try {
            $selectedArrival = \Carbon\Carbon::parse($selectedStartsAt);
            $selectedArrivalDate = $selectedArrival->toDateString();
            $selectedArrivalValue = $selectedArrival->format('Y-m-d\TH:i');
        } catch (\Throwable) {
            $selectedArrivalDate = now()->toDateString();
        }
    }
@endphp

@section('content')
    <section class="border-b border-stone-200 bg-white">
        <div class="page-section py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="section-kicker">Reservation</p>
                    <h1 class="page-title mt-3">Complete your booking request</h1>
                    <p class="lede mt-3">Share the essentials and the reservations team will review availability before confirming your stay.</p>
                </div>
                <ol class="flex flex-wrap gap-2 text-xs font-semibold text-stone-500" aria-label="Booking progress">
                    <li class="rounded-full bg-stone-950 px-3 py-1.5 text-white">Room</li>
                    <li class="rounded-full border border-stone-200 px-3 py-1.5">Guest Details</li>
                    <li class="rounded-full border border-stone-200 px-3 py-1.5">Review</li>
                    <li class="rounded-full border border-stone-200 px-3 py-1.5">Confirmation</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="page-section">
        <form method="POST" action="{{ route('bookings.store') }}" class="grid gap-6 xl:grid-cols-[1fr_380px] xl:items-start" data-reservation-form data-availability-url="{{ route('availability.check') }}">
            @csrf

            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="section-title text-xl">Guest and stay details</h2>
                        <p class="mt-2 text-sm text-stone-600">Required fields are marked by the browser and validated by the reservation system.</p>
                    </div>
                </div>

                <div class="grid gap-5 p-5 sm:grid-cols-2">
                    <label class="field-label sm:col-span-2">
                        Room experience
                        <select class="field-control" name="service_id" required data-room-select>
                            <option value="">Select a room</option>
                            @foreach ($services as $service)
                                <option
                                    value="{{ $service->id }}"
                                    data-room-name="{{ $service->name }}"
                                    data-room-rate="{{ $service->formatted_price }}"
                                    @selected($selectedServiceId == $service->id)
                                >
                                    {{ $service->name }} | {{ $service->duration_minutes }} min | {{ $service->formatted_price }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <section class="sm:col-span-2" aria-live="polite">
                        <div class="rounded-lg border border-dashed border-stone-300 bg-stone-50 p-5 {{ $selectedServiceId ? 'hidden' : '' }}" data-room-empty>
                            <p class="text-sm font-semibold text-stone-950">Select a room to preview your stay.</p>
                            <p class="mt-2 text-sm leading-6 text-stone-600">The room gallery, amenities, rate, and key details will appear here before you submit the request.</p>
                        </div>

                        @foreach ($services as $service)
                            <article class="selected-room-preview {{ $selectedServiceId == $service->id ? '' : 'hidden' }}" data-room-preview-panel="{{ $service->id }}">
                                <div class="grid gap-0 overflow-hidden rounded-lg border border-stone-200 bg-white lg:grid-cols-[1.05fr_0.95fr]">
                                    <div class="relative min-h-80 bg-stone-100" data-carousel aria-label="{{ $service->name }} selected room gallery">
                                        @foreach ($service->gallery as $index => $imageUrl)
                                            <img
                                                src="{{ $imageUrl }}"
                                                alt="{{ $index === 0 ? $service->name.' room gallery image' : '' }}"
                                                class="carousel-slide {{ $index === 0 ? 'is-active' : '' }} h-full w-full object-cover"
                                                data-carousel-slide
                                            >
                                        @endforeach
                                        <button class="carousel-control left-3" type="button" data-carousel-prev aria-label="Previous {{ $service->name }} image">
                                            <span aria-hidden="true">&lsaquo;</span>
                                        </button>
                                        <button class="carousel-control right-3" type="button" data-carousel-next aria-label="Next {{ $service->name }} image">
                                            <span aria-hidden="true">&rsaquo;</span>
                                        </button>
                                        <div class="absolute bottom-3 left-3 flex gap-1.5">
                                            @foreach ($service->gallery as $index => $imageUrl)
                                                <button class="carousel-dot carousel-dot-light {{ $index === 0 ? 'is-active' : '' }}" type="button" data-carousel-dot="{{ $index }}" aria-label="Show {{ $service->name }} image {{ $index + 1 }}"></button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="section-kicker">Selected room</p>
                                                <h3 class="mt-3 text-2xl font-semibold leading-tight text-stone-950">{{ $service->name }}</h3>
                                            </div>
                                            <span class="rounded-md bg-stone-950 px-3 py-2 text-sm font-semibold text-white">{{ $service->formatted_price }}</span>
                                        </div>
                                        <p class="mt-4 text-sm leading-6 text-stone-600">{{ $service->description }}</p>

                                        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                            <div class="rounded-md border border-stone-200 p-3">
                                                <dt class="text-stone-500">Stay window</dt>
                                                <dd class="mt-1 font-semibold text-stone-950">{{ $service->duration_minutes }} min</dd>
                                            </div>
                                            <div class="rounded-md border border-stone-200 p-3">
                                                <dt class="text-stone-500">Review status</dt>
                                                <dd class="mt-1 font-semibold text-stone-950">Pending</dd>
                                            </div>
                                        </dl>

                                        <div class="mt-5">
                                            <p class="text-sm font-semibold text-stone-950">Amenities included</p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach ($service->amenities as $amenity)
                                                    <span class="rounded-full border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600">{{ $amenity }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    <label class="field-label">
                        Full name
                        <input class="field-control" name="customer_name" value="{{ old('customer_name') }}" maxlength="120" autocomplete="name" required>
                        @error('customer_name') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Email address
                        <input class="field-control" type="email" name="customer_email" value="{{ old('customer_email') }}" maxlength="180" autocomplete="email" required>
                        @error('customer_email') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Phone
                        <input class="field-control" type="tel" name="customer_phone" value="{{ old('customer_phone') }}" maxlength="40" autocomplete="tel">
                        @error('customer_phone') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Arrival date
                        <input class="field-control" type="date" value="{{ $selectedArrivalDate }}" min="{{ now()->toDateString() }}" required data-arrival-date>
                    </label>

                    <label class="field-label">
                        Arrival time
                        <select class="field-control" required data-reservation-arrival-select data-selected-arrival="{{ $selectedArrivalValue }}">
                            <option value="">Select a room and date first</option>
                        </select>
                        <input type="hidden" name="starts_at" value="{{ $selectedArrivalValue }}" data-starts-at-input>
                        @error('starts_at') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label sm:col-span-2">
                        Requests or arrival notes
                        <textarea class="field-control min-h-32" name="notes" maxlength="1000" placeholder="Airport transfer, accessibility needs, late arrival, or special occasion">{{ old('notes') }}</textarea>
                        @error('notes') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>

            <aside class="panel lg:sticky lg:top-24" aria-label="Booking summary">
                <div class="p-5">
                    <p class="section-kicker">Summary</p>
                    <h2 class="mt-3 text-xl font-semibold text-stone-950">Your request</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-3">
                            <dt class="text-stone-500">Room</dt>
                            <dd class="text-right font-medium text-stone-950" data-summary-room>
                                {{ optional($services->firstWhere('id', (int) $selectedServiceId))->name ?? 'Select a room' }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-3">
                            <dt class="text-stone-500">Status</dt>
                            <dd><span class="badge badge-pending">Pending review</span></dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-3">
                            <dt class="text-stone-500">Rate</dt>
                            <dd class="text-right font-medium text-stone-950" data-summary-rate>
                                {{ optional($services->firstWhere('id', (int) $selectedServiceId))->formatted_price ?? 'Shown by room' }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-stone-500">Next step</dt>
                            <dd class="max-w-44 text-right font-medium text-stone-950">Team confirms availability</dd>
                        </div>
                    </dl>

                    <div class="mt-6 grid gap-3">
                        <button type="submit" class="btn btn-primary w-full" data-reservation-submit>Submit Request</button>
                        <a class="btn btn-secondary w-full" href="{{ route('rooms.index') }}">Back to Rooms</a>
                    </div>
                </div>
            </aside>
        </form>
    </section>
@endsection
