@extends('layouts.app', [
    'title' => 'Aurelia Hotel | Luxury Stays',
    'section' => 'home',
    'description' => 'Discover Aurelia Hotel, a calm boutique city stay with refined rooms and attentive reservations.',
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

    $reviews = [
        [
            'quote' => 'A beautiful room, easy check-in, and a team that handled every detail before we arrived.',
            'guest' => 'Weekend guest',
            'topic' => 'Room preparation',
        ],
        [
            'quote' => 'The location made the whole trip simple. Breakfast, meetings, and dinner were all close by.',
            'guest' => 'Business traveler',
            'topic' => 'Location',
        ],
        [
            'quote' => 'The room gallery helped us choose quickly, and the booking request was refreshingly clear.',
            'guest' => 'Returning guest',
            'topic' => 'Booking flow',
        ],
        [
            'quote' => 'Our late arrival was handled calmly, and the room felt ready the second we opened the door.',
            'guest' => 'Late arrival guest',
            'topic' => 'Arrival support',
        ],
        [
            'quote' => 'The staff remembered our requests and made the stay feel personal without ever interrupting the trip.',
            'guest' => 'Couple stay',
            'topic' => 'Service',
        ],
    ];

@endphp

@section('content')
    <section id="home" class="scroll-mt-28 bg-white" data-nav-section="home">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8 lg:py-16">
            <div class="flex flex-col justify-center" data-scroll-reveal>
                <p class="section-kicker">Aurelia Hotel</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-semibold leading-tight text-stone-950 sm:text-5xl lg:text-6xl">
                    Wake up rested. Explore easily. Eat well. Sleep well in the heart of the city.
                </h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-stone-700 sm:text-lg">A calm boutique stay with considered rooms, warm service, and simple reservations from first look to confirmation.</p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a class="btn btn-booking" href="{{ route('bookings.create') }}">Book now</a>
                    <a class="btn btn-secondary bg-white/70" href="#contact">Contact</a>
                </div>

                <dl class="mt-10 grid gap-5 border-t border-stone-300/70 pt-6 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-stone-600">Waterfront walk</dt>
                        <dd class="mt-1 text-2xl font-semibold text-stone-950">2 min</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-stone-600">Dining nearby</dt>
                        <dd class="mt-1 text-2xl font-semibold text-stone-950">100+</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-stone-600">Guest support</dt>
                        <dd class="mt-1 text-2xl font-semibold text-stone-950">24 hr</dd>
                    </div>
                </dl>
            </div>

            <div class="hero-carousel" data-carousel data-carousel-autoplay="true" data-scroll-reveal aria-label="Aurelia Hotel photography">
                <div class="relative min-h-[430px] overflow-hidden rounded-lg bg-stone-900 shadow-2xl lg:min-h-[560px]">
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

    <section class="bg-white" data-scroll-reveal>
        <div class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
            <form id="availability-form" class="availability-strip" action="{{ route('availability.check') }}" method="GET">
                <label class="availability-label">
                    <span>Arrival</span>
                    <input class="availability-control" type="date" name="date" min="{{ now()->toDateString() }}" required>
                </label>
                <button class="btn btn-primary min-h-10 w-full px-4 py-2 sm:w-auto" type="submit">Check</button>
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

    <section id="experience" class="scroll-mt-28 bg-stone-50/70">
        <div class="page-section">
            <div class="grid gap-10 lg:grid-cols-[0.72fr_1.28fr] lg:items-center">
                <div data-scroll-reveal>
                    <p class="section-kicker">Experience</p>
                    <h2 class="mt-3 text-3xl font-semibold leading-tight text-stone-950 sm:text-4xl">Guest stays, told by the people who arrived.</h2>
                    <p class="lede mt-5">Reviews highlight what matters most at Aurelia: clear booking, a central location, prepared rooms, and staff who keep every arrival calm.</p>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="kpi-card kpi-card-featured">
                            <span class="kpi-value">4.9</span>
                            <span class="kpi-label">Average rating</span>
                            <span class="kpi-note">Across recent guest stays</span>
                        </div>
                        <div class="kpi-card">
                            <span class="kpi-value">98%</span>
                            <span class="kpi-label">Recommend</span>
                            <span class="kpi-note">Would stay with us again</span>
                        </div>
                        <div class="kpi-card">
                            <span class="kpi-value">24h</span>
                            <span class="kpi-label">Support</span>
                            <span class="kpi-note">Arrival help when plans shift</span>
                        </div>
                    </div>
                </div>

                <div class="review-carousel" data-carousel data-carousel-autoplay="true" data-scroll-reveal aria-label="Customer reviews">
                    <div class="review-stage">
                        <div class="review-stage-header">
                            <div>
                                <p class="section-kicker">Customer reviews</p>
                                <p class="mt-1 text-sm text-stone-500">Live impressions from recent stays</p>
                            </div>
                            <span class="review-score">4.9/5</span>
                        </div>

                        <div class="relative min-h-[300px] overflow-hidden">
                        @foreach ($reviews as $index => $review)
                            <figure class="testimonial-card testimonial-slide {{ $index === 0 ? 'is-active' : '' }}" data-carousel-slide>
                                <span class="review-quote-mark" aria-hidden="true">"</span>
                                <blockquote>"{{ $review['quote'] }}"</blockquote>
                                <figcaption>
                                    <span>{{ $review['guest'] }}</span>
                                    <span>{{ $review['topic'] }}</span>
                                </figcaption>
                            </figure>
                        @endforeach

                            <button class="carousel-control testimonial-control testimonial-control-prev" type="button" data-carousel-prev aria-label="Previous customer review">
                                <span aria-hidden="true">&lsaquo;</span>
                            </button>
                            <button class="carousel-control testimonial-control testimonial-control-next" type="button" data-carousel-next aria-label="Next customer review">
                                <span aria-hidden="true">&rsaquo;</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center gap-2">
                        <div class="flex gap-2" aria-label="Choose customer review">
                            @foreach ($reviews as $index => $review)
                                <button class="carousel-dot {{ $index === 0 ? 'is-active' : '' }}" type="button" data-carousel-dot="{{ $index }}" aria-label="Show review from {{ $review['guest'] }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="page-section">
            <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-stretch">
                <div class="flex flex-col justify-center" data-scroll-reveal>
                    <p class="section-kicker">Location</p>
                    <h2 class="section-title mt-3">Find us in the harbor district.</h2>
                    <p class="lede mt-4">Aurelia Hotel is positioned for easy arrivals, waterfront walks, and quick access to dining, galleries, shopping, and the city center.</p>
                    <div class="mt-6 grid gap-4 text-sm text-stone-600 sm:grid-cols-2">
                        <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                            <span class="block font-semibold text-stone-950">Aurelia Hotel</span>
                            <span class="mt-1 block">Harbor district</span>
                        </div>
                        <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                            <span class="block font-semibold text-stone-950">Nearby</span>
                            <span class="mt-1 block">Waterfront, restaurants, galleries, and transit</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-stone-200 bg-stone-100 shadow-sm" data-scroll-reveal>
                    <iframe
                        title="Google Map showing Aurelia Hotel in the harbor district"
                        src="https://www.google.com/maps?q=Aurelia%20Hotel%20Harbor%20district&output=embed"
                        class="h-[420px] w-full"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
