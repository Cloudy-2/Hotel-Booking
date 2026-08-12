@extends('layouts.app', [
    'title' => 'Our Rooms | Aurelia Hotel',
    'section' => 'rooms',
    'description' => 'Explore Aurelia Hotel room experiences, image galleries, amenities, and booking rates.',
    'canonicalUrl' => route('rooms.index'),
])

@section('content')
    <section class="bg-stone-50">
        <div class="page-section grid gap-6 lg:grid-cols-[0.8fr_1.2fr] lg:items-end">
            <div data-scroll-reveal>
                <p class="section-kicker">Our rooms</p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight text-stone-950 sm:text-5xl">Curated room experiences.</h1>
            </div>
            <p class="max-w-2xl text-base leading-7 text-stone-700 lg:justify-self-end" data-scroll-reveal>Compare rooms by atmosphere, rate, and key comforts. Every room includes an image carousel so guests can browse the space before booking.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($services as $service)
                <article class="room-card" data-scroll-reveal>
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
                                <h2 class="text-lg font-semibold text-stone-950">{{ $service->name }}</h2>
                                <p class="mt-1 text-sm text-stone-500">Up to {{ $service->max_guests ?? 2 }} guests | {{ $service->room_size ?? 'Size on request' }}</p>
                            </div>
                            <span class="whitespace-nowrap text-base font-semibold text-stone-950">{{ $service->formatted_price }}</span>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-medium text-stone-600">
                            @foreach ($service->amenities as $amenity)
                                <span class="rounded-full border border-stone-200 px-3 py-1">{{ $amenity }}</span>
                            @endforeach
                        </div>
                        <a class="btn btn-primary mt-5 w-full" href="{{ route('bookings.create', ['service_id' => $service->id]) }}">Select Room</a>
                    </div>
                </article>
            @empty
                <div class="empty-state panel md:col-span-2 xl:col-span-3">
                    <h2 class="text-lg font-semibold text-stone-950">No rooms are available right now.</h2>
                    <p class="mt-2 text-sm text-stone-600">Please check back soon or contact the front desk for assistance.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
