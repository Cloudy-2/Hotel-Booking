@extends('layouts.app', [
    'title' => 'Aurelia Hotel | Luxury Stays',
    'section' => 'guest',
    'description' => 'Explore Aurelia Hotel rooms and begin a calm, premium reservation request.',
    'canonicalUrl' => route('home'),
])

@php
    $heroSlides = [
        [
            'src' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Elegant hotel pool and suites at dusk',
            'label' => 'Arrival court',
            'caption' => 'Warm light, calm service, and a first impression designed to settle the day.',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Luxury hotel lounge with refined seating',
            'label' => 'Private lounge',
            'caption' => 'Quiet corners for slow mornings, unhurried meetings, and late evening plans.',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Premium hotel room with soft bedding',
            'label' => 'Guest rooms',
            'caption' => 'Restful rooms with considered details, generous light, and practical comfort.',
        ],
    ];
@endphp

@section('content')
    <section class="bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8 lg:py-14">
            <div class="flex flex-col justify-center">
                <p class="section-kicker">Private city hotel</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-semibold leading-tight text-stone-950 sm:text-5xl lg:text-6xl">A quieter way to arrive, stay, and unwind.</h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-stone-600 sm:text-lg">Aurelia pairs refined rooms with a reservation experience that stays clear from the first search to the final confirmation.</p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a class="btn btn-primary" href="{{ route('bookings.create') }}">Reserve Your Stay</a>
                    <a class="btn btn-secondary" href="#rooms">View Rooms</a>
                </div>

                <dl class="mt-10 grid gap-5 border-t border-stone-200 pt-6 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-stone-500">Guest rating</dt>
                        <dd class="mt-1 text-2xl font-semibold text-stone-950">4.9/5</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-stone-500">Check-in support</dt>
                        <dd class="mt-1 text-2xl font-semibold text-stone-950">24 hr</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-stone-500">Rooms prepared</dt>
                        <dd class="mt-1 text-2xl font-semibold text-stone-950">Daily</dd>
                    </div>
                </dl>
            </div>

            <div class="hero-carousel" data-carousel data-carousel-autoplay="true" aria-label="Aurelia Hotel photography">
                <div class="relative min-h-[460px] overflow-hidden rounded-lg bg-stone-900 lg:min-h-[580px]">
                    @foreach ($heroSlides as $index => $slide)
                        <figure class="carousel-slide {{ $index === 0 ? 'is-active' : '' }}" data-carousel-slide>
                            <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" class="h-full w-full object-cover">
                            <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/90 via-stone-950/45 to-transparent p-6 text-white sm:p-8">
                                <p class="text-sm font-semibold text-stone-200">{{ $slide['label'] }}</p>
                                <p class="mt-2 max-w-lg text-2xl font-semibold leading-tight">{{ $slide['caption'] }}</p>
                            </figcaption>
                        </figure>
                    @endforeach

                    <button class="carousel-control left-4" type="button" data-carousel-prev aria-label="Previous hotel image">
                        <span aria-hidden="true">&lsaquo;</span>
                    </button>
                    <button class="carousel-control right-4" type="button" data-carousel-next aria-label="Next hotel image">
                        <span aria-hidden="true">&rsaquo;</span>
                    </button>
                </div>

                <div class="mt-4 flex items-center justify-between gap-4">
                    <div class="flex gap-2" aria-label="Choose hero image">
                        @foreach ($heroSlides as $index => $slide)
                            <button class="carousel-dot {{ $index === 0 ? 'is-active' : '' }}" type="button" data-carousel-dot="{{ $index }}" aria-label="Show {{ $slide['label'] }}"></button>
                        @endforeach
                    </div>
                    <p class="hidden text-sm text-stone-500 sm:block">Curated spaces for composed city stays</p>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-stone-200 bg-stone-100">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <form id="availability-form" class="grid gap-4 rounded-lg border border-stone-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_auto]" action="{{ route('availability.check') }}" method="GET">
                <label class="field-label">
                    Arrival date
                    <input class="field-control" type="date" name="date" min="{{ now()->toDateString() }}" required>
                </label>
                <div class="flex items-end">
                    <button class="btn btn-primary w-full md:w-auto" type="submit">Check Availability</button>
                </div>
            </form>
        </div>
    </section>

    <div id="availability-modal" class="modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="availability-title">
        <div class="modal-panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Availability</p>
                    <h2 id="availability-title" class="mt-3 text-2xl font-semibold text-stone-950">Available rooms</h2>
                    <p id="availability-message" class="mt-2 text-sm text-stone-600"></p>
                </div>
                <button id="availability-modal-action" class="btn btn-secondary min-h-9 px-3 py-1.5" type="button" data-modal-close>Close</button>
            </div>
            <div id="availability-results" class="max-h-[65vh] overflow-auto"></div>
        </div>
    </div>

    <section id="rooms" class="page-section scroll-mt-24">
        <div class="grid gap-6 lg:grid-cols-[0.85fr_1.15fr] lg:items-end">
            <div>
                <p class="section-kicker">Rooms</p>
                <h2 class="section-title mt-3">Curated room experiences</h2>
            </div>
            <p class="max-w-2xl text-base leading-7 text-stone-600 lg:justify-self-end">Compare rooms by atmosphere, rate, and key comforts. Each image feature can be browsed without leaving the page.</p>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($services as $service)
                <article class="room-card">
                    <div class="relative h-64 overflow-hidden bg-stone-100" data-carousel aria-label="{{ $service->name }} gallery">
                        @foreach ($service->gallery as $index => $imageUrl)
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $index === 0 ? $service->name.' room view' : '' }}"
                                class="carousel-slide {{ $index === 0 ? 'is-active' : '' }} h-full w-full object-cover"
                                data-carousel-slide
                            >
                        @endforeach
                        <button class="carousel-control left-3 size-9 text-xl" type="button" data-carousel-prev aria-label="Previous {{ $service->name }} image">
                            <span aria-hidden="true">&lsaquo;</span>
                        </button>
                        <button class="carousel-control right-3 size-9 text-xl" type="button" data-carousel-next aria-label="Next {{ $service->name }} image">
                            <span aria-hidden="true">&rsaquo;</span>
                        </button>
                        <div class="absolute bottom-3 left-3 flex gap-1.5">
                            @foreach ($service->gallery as $index => $imageUrl)
                                <button class="carousel-dot carousel-dot-light {{ $index === 0 ? 'is-active' : '' }}" type="button" data-carousel-dot="{{ $index }}" aria-label="Show room image {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-stone-950">{{ $service->name }}</h3>
                                <p class="mt-1 text-sm text-stone-500">Up to {{ $service->max_guests ?? 2 }} guests · {{ $service->room_size ?? 'Size on request' }}</p>
                            </div>
                            <span class="whitespace-nowrap text-base font-semibold text-stone-950">{{ $service->formatted_price }}</span>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-medium text-stone-600">
                            @foreach (array_slice($service->amenities, 0, 3) as $amenity)
                                <span class="rounded-full border border-stone-200 px-3 py-1">{{ $amenity }}</span>
                            @endforeach
                        </div>
                        <a class="btn btn-primary mt-5 w-full" href="{{ route('bookings.create', ['service_id' => $service->id]) }}">Select Room</a>
                    </div>
                </article>
            @empty
                <div class="empty-state panel md:col-span-2 xl:col-span-3">
                    <h3 class="text-lg font-semibold text-stone-950">No rooms are available right now.</h3>
                    <p class="mt-2 text-sm text-stone-600">Please check back soon or contact the front desk for assistance.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section id="experience" class="scroll-mt-24 bg-white">
        <div class="page-section grid gap-10 lg:grid-cols-[0.85fr_1.15fr]">
            <div>
                <p class="section-kicker">Experience</p>
                <h2 class="section-title mt-3">A stay shaped around ease.</h2>
                <p class="lede mt-4">The best luxury interfaces are quiet: clear decisions, useful details, and no friction when guests are ready to book.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="border-t border-stone-200 pt-5">
                    <h3 class="font-semibold text-stone-950">Transparent rates</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Prices stay visible and easy to compare before the guest enters their details.</p>
                </div>
                <div class="border-t border-stone-200 pt-5">
                    <h3 class="font-semibold text-stone-950">Calm reservation flow</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">The booking step asks for only the information needed to review the request.</p>
                </div>
                <div class="border-t border-stone-200 pt-5">
                    <h3 class="font-semibold text-stone-950">Responsive browsing</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Image galleries, forms, and calls to action stay comfortable on mobile screens.</p>
                </div>
                <div class="border-t border-stone-200 pt-5">
                    <h3 class="font-semibold text-stone-950">Managed confirmation</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Reservation staff can review requests and keep guest status clear.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
