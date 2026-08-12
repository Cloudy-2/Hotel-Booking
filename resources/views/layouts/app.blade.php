@php
    $title = $title ?? 'Aurelia Hotel';
    $section = $section ?? 'guest';
    $description = $description ?? 'A refined hotel booking experience for private stays, calm arrivals, and attentive reservations.';
    $canonicalUrl = $canonicalUrl ?? url()->current();
    $isLanding = request()->routeIs('home');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('site-icon.svg') }}">
    <meta property="og:site_name" content="Aurelia Hotel">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <title>{{ $title }}</title>

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-950 antialiased">
    <a class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-stone-950 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white" href="#content">
        Skip to content
    </a>

    <div class="min-h-screen">
        <header class="site-header {{ $isLanding ? 'site-header-landing' : '' }}" data-site-header>
            <div class="site-header-inner">
                <a href="{{ route('home') }}" class="brand-mark" aria-label="Aurelia Hotel home">
                    <img src="{{ asset('site-icon.svg') }}" alt="" class="brand-icon">
                    <span>
                        <span class="block text-sm font-semibold leading-none tracking-normal text-stone-950 sm:text-base">Aurelia Hotel</span>
                        <span class="mt-1 hidden text-xs font-medium text-stone-500 sm:block">Private stays and reservations</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Primary navigation">
                    <a class="nav-link {{ $section === 'home' ? 'nav-link-active' : '' }}" href="{{ route('home') }}" data-nav-link="home">Home</a>
                    <a class="nav-link {{ $section === 'hotel' ? 'nav-link-active' : '' }}" href="{{ route('hotel.show') }}" data-nav-link="hotel">The hotel</a>
                    <a class="nav-link {{ $section === 'rooms' ? 'nav-link-active' : '' }}" href="{{ route('rooms.index') }}" data-nav-link="rooms">Our rooms</a>
                    <a class="nav-link {{ $section === 'dining' ? 'nav-link-active' : '' }}" href="{{ route('dining.index') }}" data-nav-link="dining">Food and drink</a>
                    <a class="nav-link {{ $section === 'contact' ? 'nav-link-active' : '' }}" href="{{ route('contact.show') }}" data-nav-link="contact">Contact</a>
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a class="nav-link {{ $section === 'admin' ? 'nav-link-active' : '' }}" href="{{ route('dashboard') }}">Admin</a>
                        @endif
                    @endauth
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a class="btn btn-secondary hidden sm:inline-flex" href="{{ route('dashboard') }}">Management</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-ghost" type="submit">Sign Out</button>
                        </form>
                    @else
                        <a class="btn btn-secondary hidden sm:inline-flex" href="{{ route('login') }}">Management</a>
                    @endauth
                    <a class="btn btn-booking" href="{{ route('bookings.create') }}">Booking</a>
                </div>
            </div>
        </header>

        @if (session('status') || $errors->any())
            <div
                class="hidden"
                data-swal-feedback
                data-type="{{ $errors->any() ? 'error' : session('feedback_type', 'success') }}"
                data-title="{{ $errors->any() ? 'A few details need attention' : session('feedback_title', 'All set') }}"
                data-message="{{ $errors->any() ? $errors->first() : session('status') }}"
            ></div>
        @endif

        <div class="sweet-alert-backdrop hidden" data-swal-modal aria-hidden="true">
            <section class="sweet-alert" role="dialog" aria-modal="true" aria-labelledby="sweet-alert-title" aria-describedby="sweet-alert-message">
                <div class="sweet-alert-icon" data-swal-icon>OK</div>
                <h2 id="sweet-alert-title" data-swal-title>Action completed</h2>
                <p id="sweet-alert-message" data-swal-message>Your action was completed.</p>
                <div class="sweet-alert-actions">
                    <button class="btn btn-secondary hidden" type="button" data-swal-cancel>Cancel</button>
                    <button class="btn btn-primary" type="button" data-swal-confirm>OK</button>
                </div>
            </section>
        </div>

        <main id="content">
            @yield('content')
        </main>

        <footer id="contact" class="border-t border-stone-200 bg-stone-950 text-stone-300">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 text-sm sm:px-6 md:grid-cols-[1.4fr_0.8fr_0.8fr_0.8fr] lg:px-8">
                <div>
                    <div class="brand-mark">
                        <img src="{{ asset('site-icon.svg') }}" alt="" class="brand-icon">
                        <span class="font-semibold text-white">Aurelia Hotel</span>
                    </div>
                    <p class="mt-5 max-w-md leading-6 text-stone-400">A refined booking experience for calm arrivals, considered rooms, and attentive hospitality.</p>
                    <a class="btn mt-6 border-stone-700 bg-stone-900 text-white hover:bg-stone-800" href="{{ route('bookings.create') }}">Reserve Your Stay</a>
                </div>
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Hotel</h2>
                    <div class="mt-4 grid gap-2">
                        <a class="hover:text-white" href="{{ route('rooms.index') }}">Rooms</a>
                        <a class="hover:text-white" href="{{ route('dining.index') }}">Food and drink</a>
                        <a class="hover:text-white" href="{{ route('hotel.show') }}">The hotel</a>
                        <a class="hover:text-white" href="{{ route('bookings.create') }}">Reservations</a>
                    </div>
                </div>
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Contact</h2>
                    <p class="mt-4 leading-6 text-stone-400">hello@aurelia.example<br>+1 (555) 018-2026</p>
                </div>
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Location</h2>
                    <p class="mt-4 leading-6 text-stone-400">Harbor district<br>Steps from dining, galleries, and the waterfront.</p>
                </div>
            </div>
            <div class="border-t border-stone-800">
                <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-xs text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                    <span>2026 Aurelia Hotel. All rights reserved.</span>
                    <span>Privacy-minded reservations and secure booking review.</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
