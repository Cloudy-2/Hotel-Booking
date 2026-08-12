@extends('layouts.app', [
    'title' => 'Food and Drink | Aurelia Hotel',
    'section' => 'dining',
    'description' => 'Explore Aurelia Hotel breakfast, drinks, and in-room dining options.',
    'canonicalUrl' => route('dining.index'),
])

@php
    $diningSlides = [
        [
            'src' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Elegant plated breakfast with coffee',
            'label' => 'Breakfast lounge',
            'caption' => 'Fresh plates, calm tables, and coffee served from early morning.',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Refined hotel bar with warm lighting',
            'label' => 'Lobby bar',
            'caption' => 'Classic drinks and a relaxed evening room for slow conversation.',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Hotel room service tray with food and drinks',
            'label' => 'In-room dining',
            'caption' => 'A compact menu for late arrivals, private meals, and quiet nights in.',
        ],
    ];
@endphp

@section('content')
    <section class="bg-stone-50">
        <div class="page-section grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div data-scroll-reveal>
                <p class="section-kicker">Food and drink</p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight text-stone-950 sm:text-5xl">Slow mornings, polished evenings.</h1>
                <p class="lede mt-5 text-stone-700">From first coffee to late dining, Aurelia keeps hospitality warm, considered, and easy to plan around your stay.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a class="btn btn-booking" href="{{ route('bookings.create') }}">Booking</a>
                    <a class="btn btn-secondary bg-white/70" href="{{ route('rooms.index') }}">Our rooms</a>
                </div>
            </div>

            <div class="hero-carousel" data-carousel data-carousel-autoplay="true" data-scroll-reveal aria-label="Food and drink photography">
                <div class="relative min-h-[430px] overflow-hidden rounded-lg bg-stone-900 shadow-2xl">
                    @foreach ($diningSlides as $index => $slide)
                        <figure class="carousel-slide {{ $index === 0 ? 'is-active' : '' }}" data-carousel-slide>
                            <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" class="h-full w-full object-cover">
                            <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/90 via-stone-950/45 to-transparent p-6 text-white">
                                <p class="text-sm font-semibold text-stone-200">{{ $slide['label'] }}</p>
                                <p class="mt-2 max-w-md text-xl font-semibold leading-tight">{{ $slide['caption'] }}</p>
                            </figcaption>
                        </figure>
                    @endforeach

                    <button class="carousel-control left-4" type="button" data-carousel-prev aria-label="Previous food and drink image">
                        <span aria-hidden="true">&lsaquo;</span>
                    </button>
                    <button class="carousel-control right-4" type="button" data-carousel-next aria-label="Next food and drink image">
                        <span aria-hidden="true">&rsaquo;</span>
                    </button>
                </div>

                <div class="mt-4 flex gap-2" aria-label="Choose food and drink image">
                    @foreach ($diningSlides as $index => $slide)
                        <button class="carousel-dot {{ $index === 0 ? 'is-active' : '' }}" type="button" data-carousel-dot="{{ $index }}" aria-label="Show {{ $slide['label'] }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="grid gap-5 md:grid-cols-3">
            <article class="panel p-6" data-scroll-reveal>
                <p class="section-kicker">Morning</p>
                <h2 class="mt-3 text-xl font-semibold text-stone-950">Breakfast lounge</h2>
                <p class="mt-3 text-sm leading-6 text-stone-600">Seasonal plates, fresh fruit, pastries, and quiet service for early starts or slow mornings.</p>
            </article>
            <article class="panel p-6" data-scroll-reveal>
                <p class="section-kicker">Evening</p>
                <h2 class="mt-3 text-xl font-semibold text-stone-950">Lobby bar</h2>
                <p class="mt-3 text-sm leading-6 text-stone-600">Classic cocktails, wine, coffee, and comfortable low-lit tables for an unhurried evening.</p>
            </article>
            <article class="panel p-6" data-scroll-reveal>
                <p class="section-kicker">Private dining</p>
                <h2 class="mt-3 text-xl font-semibold text-stone-950">In-room service</h2>
                <p class="mt-3 text-sm leading-6 text-stone-600">A compact menu for arrivals, late work, and relaxed meals without leaving the room.</p>
            </article>
        </div>
    </section>
@endsection
