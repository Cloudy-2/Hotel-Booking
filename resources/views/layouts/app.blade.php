@php
    $title = $title ?? 'Aurelia Hotel';
    $section = $section ?? 'guest';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <header class="site-header">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="brand-mark" aria-label="Aurelia Hotel home">
                    <span class="brand-icon" aria-hidden="true">A</span>
                    <span>
                        <span class="block text-sm font-semibold leading-none tracking-normal text-stone-950">Aurelia Hotel</span>
                        <span class="mt-1 block text-xs font-medium text-stone-500">Private stays and reservations</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Primary navigation">
                    <a class="nav-link {{ $section === 'guest' ? 'nav-link-active' : '' }}" href="{{ route('home') }}">Stay</a>
                    <a class="nav-link {{ request()->routeIs('bookings.create') ? 'nav-link-active' : '' }}" href="{{ route('bookings.create') }}">Reserve</a>
                    <a class="nav-link {{ $section === 'admin' ? 'nav-link-active' : '' }}" href="{{ route('dashboard') }}">Admin</a>
                </nav>

                <div class="flex items-center gap-2">
                    <a class="btn btn-secondary hidden sm:inline-flex" href="{{ route('dashboard') }}">Management</a>
                    <a class="btn btn-primary" href="{{ route('bookings.create') }}">Book Now</a>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                <div class="alert alert-success" role="status">{{ session('status') }}</div>
            </div>
        @endif

        <main id="content">
            @yield('content')
        </main>

        <footer class="border-t border-stone-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 text-sm text-stone-600 sm:px-6 md:grid-cols-[1.3fr_1fr_1fr] lg:px-8">
                <div>
                    <div class="brand-mark">
                        <span class="brand-icon" aria-hidden="true">A</span>
                        <span class="font-semibold text-stone-950">Aurelia Hotel</span>
                    </div>
                    <p class="mt-4 max-w-md leading-6">A refined booking experience for calm arrivals, considered rooms, and attentive hospitality.</p>
                </div>
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Contact</h2>
                    <p class="mt-4 leading-6">hello@aurelia.example<br>+1 (555) 018-2026</p>
                </div>
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Location</h2>
                    <p class="mt-4 leading-6">Harbor district<br>Steps from dining, galleries, and the waterfront.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
