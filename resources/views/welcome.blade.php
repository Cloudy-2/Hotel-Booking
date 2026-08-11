@extends('layouts.app', ['title' => 'Aurelia Hotel | Luxury Stays', 'section' => 'guest'])

@section('content')
    <section class="bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-16">
            <div class="flex flex-col justify-center">
                <p class="section-kicker">Private city hotel</p>
                <h1 class="display-title mt-4">Quiet luxury for stays that should feel effortless.</h1>
                <p class="lede mt-5">Choose a room, share your arrival details, and let the team prepare the rest. Aurelia keeps booking clear, calm, and personal from the first click.</p>

                <div class="mt-8 grid gap-3 text-sm text-stone-600 sm:grid-cols-3">
                    <div class="border-l border-stone-300 pl-4">
                        <strong class="block text-base text-stone-950">24-hour desk</strong>
                        Thoughtful arrival support
                    </div>
                    <div class="border-l border-stone-300 pl-4">
                        <strong class="block text-base text-stone-950">Central address</strong>
                        Near dining and galleries
                    </div>
                    <div class="border-l border-stone-300 pl-4">
                        <strong class="block text-base text-stone-950">Flexible rooms</strong>
                        Solo, couple, and suite stays
                    </div>
                </div>
            </div>

            <div class="relative min-h-[420px] overflow-hidden rounded-lg bg-stone-900">
                <img
                    src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80"
                    alt="Elegant hotel pool and suites at dusk"
                    class="absolute inset-0 h-full w-full object-cover opacity-90"
                >
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/85 to-transparent p-6 text-white">
                    <p class="text-sm font-medium text-stone-200">Tonight's atmosphere</p>
                    <p class="mt-2 max-w-md text-2xl font-semibold leading-tight">Warm light, quiet rooms, and a front desk that already knows what matters.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-stone-200 bg-stone-100">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <form class="grid gap-4 rounded-lg border border-stone-200 bg-white p-4 md:grid-cols-[1fr_1fr_1fr_auto]" action="{{ route('bookings.create') }}" method="GET">
                <label class="field-label">
                    Check-in
                    <input class="field-control" type="date" name="check_in">
                </label>
                <label class="field-label">
                    Check-out
                    <input class="field-control" type="date" name="check_out">
                </label>
                <label class="field-label">
                    Guests
                    <select class="field-control" name="guests">
                        <option>1 guest</option>
                        <option selected>2 guests</option>
                        <option>3 guests</option>
                        <option>4 guests</option>
                    </select>
                </label>
                <div class="flex items-end">
                    <button class="btn btn-primary w-full md:w-auto" type="submit">Check Availability</button>
                </div>
            </form>
        </div>
    </section>

    <section class="page-section">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-kicker">Rooms</p>
                <h2 class="section-title mt-3">Curated room experiences</h2>
                <p class="lede mt-3">Each option keeps the essentials easy to compare: atmosphere, duration window, and starting rate.</p>
            </div>
            <a class="btn btn-secondary" href="{{ route('bookings.create') }}">Reserve a Room</a>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @forelse ($services as $service)
                <article class="room-card">
                    <img
                        src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=900&q=80"
                        alt="Refined hotel room with neutral bedding"
                        class="room-image"
                    >
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="text-lg font-semibold text-stone-950">{{ $service->name }}</h3>
                            <span class="whitespace-nowrap text-sm font-semibold text-stone-950">{{ $service->formatted_price }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-stone-600">{{ $service->description }}</p>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-medium text-stone-600">
                            <span class="rounded-full border border-stone-200 px-3 py-1">{{ $service->duration_minutes }} min stay consult</span>
                            <span class="rounded-full border border-stone-200 px-3 py-1">Private booking</span>
                        </div>
                        <a class="btn btn-primary mt-5 w-full" href="{{ route('bookings.create', ['service_id' => $service->id]) }}">Select Room</a>
                    </div>
                </article>
            @empty
                <div class="empty-state panel md:col-span-3">
                    <h3 class="text-lg font-semibold text-stone-950">No rooms are available right now.</h3>
                    <p class="mt-2 text-sm text-stone-600">Please check back soon or contact the front desk for assistance.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-white">
        <div class="page-section grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
                <p class="section-kicker">Hospitality</p>
                <h2 class="section-title mt-3">Designed for clarity before arrival.</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="border-t border-stone-200 pt-4">
                    <h3 class="font-semibold text-stone-950">Transparent totals</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Rates stay visible through the reservation flow so guests know what they selected.</p>
                </div>
                <div class="border-t border-stone-200 pt-4">
                    <h3 class="font-semibold text-stone-950">Calm forms</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Only necessary guest details are requested, with clear labels and validation.</p>
                </div>
                <div class="border-t border-stone-200 pt-4">
                    <h3 class="font-semibold text-stone-950">Managed requests</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Reservation teams can confirm or cancel requests from a focused admin workspace.</p>
                </div>
                <div class="border-t border-stone-200 pt-4">
                    <h3 class="font-semibold text-stone-950">Mobile ready</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Layouts, forms, cards, and tables adapt cleanly for smaller screens.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
