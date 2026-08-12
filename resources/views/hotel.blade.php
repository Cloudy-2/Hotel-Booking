@extends('layouts.app', [
    'title' => 'The Hotel | Aurelia Hotel',
    'section' => 'hotel',
    'description' => 'Learn about Aurelia Hotel, a calm boutique city hotel with personal service and a central waterfront location.',
    'canonicalUrl' => route('hotel.show'),
])

@php
    $hotelSlides = [
        [
            'src' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Refined hotel lounge with warm seating',
            'label' => 'Private lounge',
            'caption' => 'Quiet corners for arrivals, meetings, and evening plans.',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Hotel pool and exterior at dusk',
            'label' => 'Arrival court',
            'caption' => 'A polished first impression with calm service from the door.',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1600011689032-8b628b8a8747?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Elegant hotel corridor and room entrance',
            'label' => 'Considered details',
            'caption' => 'Simple wayfinding, soft lighting, and spaces made for unhurried stays.',
        ],
    ];
@endphp

@section('content')
    <section class="bg-stone-50">
        <div class="page-section grid gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
            <div data-scroll-reveal>
                <p class="section-kicker">The hotel</p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight text-stone-950 sm:text-5xl">Welcome to our boutique hotel.</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-stone-700">Aurelia is designed for guests who want a polished city base without the noise: personal service, practical details, and rooms that feel settled from the moment you arrive.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a class="btn btn-booking" href="{{ route('bookings.create') }}">Booking</a>
                    <a class="btn btn-secondary bg-white/70" href="{{ route('rooms.index') }}">Our rooms</a>
                </div>
            </div>

            <div class="hero-carousel" data-carousel data-carousel-autoplay="true" data-scroll-reveal aria-label="Aurelia Hotel spaces">
                <div class="relative min-h-[430px] overflow-hidden rounded-lg bg-stone-900 shadow-2xl">
                    @foreach ($hotelSlides as $index => $slide)
                        <figure class="carousel-slide {{ $index === 0 ? 'is-active' : '' }}" data-carousel-slide>
                            <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" class="h-full w-full object-cover">
                            <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/90 via-stone-950/45 to-transparent p-6 text-white">
                                <p class="text-sm font-semibold text-stone-200">{{ $slide['label'] }}</p>
                                <p class="mt-2 max-w-md text-xl font-semibold leading-tight">{{ $slide['caption'] }}</p>
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

                <div class="mt-4 flex gap-2" aria-label="Choose hotel image">
                    @foreach ($hotelSlides as $index => $slide)
                        <button class="carousel-dot {{ $index === 0 ? 'is-active' : '' }}" type="button" data-carousel-dot="{{ $index }}" aria-label="Show {{ $slide['label'] }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="page-section">
            <div class="grid gap-6 md:grid-cols-3">
                <article class="feature-column" data-scroll-reveal>
                    <h2>Unique and personal</h2>
                    <p>Each room is arranged around comfort, light, and privacy, with enough detail to feel distinctive without becoming fussy.</p>
                </article>
                <article class="feature-column" data-scroll-reveal>
                    <h2>The extra mile</h2>
                    <p>Our team keeps arrivals, special requests, and reservation follow-up clear so guests can focus on the stay itself.</p>
                </article>
                <article class="feature-column" data-scroll-reveal>
                    <h2>Easy movement</h2>
                    <p>Step out to dining, galleries, shopping, and the waterfront, then return to a quiet lobby and prepared room.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
