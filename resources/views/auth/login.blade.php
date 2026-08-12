@extends('layouts.app', [
    'title' => 'Management Sign In | Aurelia Hotel',
    'section' => 'admin',
    'description' => 'Sign in to manage Aurelia Hotel reservations, room experiences, and guest requests.',
])

@php
    $loginImages = [
        [
            'src' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1800&q=80',
            'alt' => 'Aurelia-style hotel pool and suites at dusk',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1800&q=80',
            'alt' => 'Luxury hotel lounge with warm lighting',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1800&q=80',
            'alt' => 'Premium hotel room with neutral bedding',
        ],
        [
            'src' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1800&q=80',
            'alt' => 'Refined hotel room prepared for guests',
        ],
    ];
@endphp

@section('content')
    <section class="login-stage">
        <div class="login-slideshow" aria-hidden="true">
            @foreach ($loginImages as $image)
                <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" class="login-slide">
            @endforeach
        </div>
        <div class="absolute inset-0 bg-stone-950/65"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950/85 via-stone-950/45 to-stone-950/25"></div>

        <div class="relative z-10 mx-auto grid min-h-[calc(100vh-73px)] max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1fr_460px] lg:px-8">
            <div class="hidden max-w-2xl text-white lg:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-200">Aurelia operations</p>
                <h1 class="mt-5 text-5xl font-semibold leading-tight">A calm control room for every guest arrival.</h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-stone-200">Review reservations, prepare rooms, and keep the service team aligned from a focused management workspace.</p>
                <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-white/20 pt-6 text-sm">
                    <div>
                        <dt class="text-stone-300">Requests</dt>
                        <dd class="mt-1 text-2xl font-semibold text-white">Live</dd>
                    </div>
                    <div>
                        <dt class="text-stone-300">Rooms</dt>
                        <dd class="mt-1 text-2xl font-semibold text-white">Managed</dd>
                    </div>
                    <div>
                        <dt class="text-stone-300">Access</dt>
                        <dd class="mt-1 text-2xl font-semibold text-white">Secure</dd>
                    </div>
                </dl>
            </div>

            <div class="login-card">
                <div class="border-b border-stone-200 px-6 py-6">
                    <p class="section-kicker">Access</p>
                    <h2 class="mt-3 text-2xl font-semibold text-stone-950">Management sign in</h2>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Enter your team credentials to manage reservations and room availability.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="grid gap-5 p-6">
                    @csrf

                    <label class="field-label">
                        Email address
                        <input class="field-control" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Password
                        <span class="relative">
                            <input class="field-control pr-12" type="password" name="password" autocomplete="current-password" required data-password-input>
                            <button class="password-toggle" type="button" data-password-toggle aria-label="Show password" aria-pressed="false">
                                <svg class="password-icon-show" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.75 12s3.25-6 9.25-6 9.25 6 9.25 6-3.25 6-9.25 6-9.25-6-9.25-6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 15.25a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg class="password-icon-hide hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m4 4 16 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M10.58 10.58a3.25 3.25 0 0 0 4.59 4.59" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M8.39 5.75A10.4 10.4 0 0 1 12 5c6 0 9.25 7 9.25 7a14.4 14.4 0 0 1-2.62 3.55" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5.73 7.7C3.78 9.38 2.75 12 2.75 12s3.25 7 9.25 7c1.39 0 2.63-.38 3.72-.97" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </span>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <label class="flex items-center gap-2 text-sm font-medium text-stone-700">
                            <input type="checkbox" name="remember" value="1" class="size-4 rounded border-stone-300 text-stone-950 focus:ring-stone-800">
                            Remember me
                        </label>
                        <span class="text-sm text-stone-500">Staff access only</span>
                    </div>

                    <button class="btn btn-primary w-full" type="submit">Sign In</button>
                </form>

                <div class="border-t border-stone-200 bg-stone-50 px-6 py-4">
                    <a class="text-sm font-semibold text-stone-700 hover:text-stone-950" href="{{ route('home') }}">Return to guest site</a>
                </div>
            </div>
        </div>
    </section>
@endsection
