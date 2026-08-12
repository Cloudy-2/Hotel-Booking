@extends('layouts.app', [
    'title' => 'Contact | Aurelia Hotel',
    'section' => 'contact',
    'description' => 'Contact Aurelia Hotel for reservation questions, arrival help, and private stay requests.',
    'canonicalUrl' => route('contact.show'),
])

@section('content')
    <section class="bg-white">
        <div class="page-section grid gap-10 lg:grid-cols-[0.72fr_1.28fr] lg:items-start">
            <div data-scroll-reveal>
                <p class="section-kicker">Contact</p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight text-stone-950 sm:text-5xl">Ask us anything.</h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-stone-600">Share your question, arrival detail, or special request. The reservations team will review it and respond as soon as possible.</p>

                <div class="mt-8 grid gap-4 text-sm">
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-5">
                        <span class="block font-semibold text-stone-950">Email</span>
                        <span class="mt-1 block text-stone-600">hello@aurelia.example</span>
                    </div>
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-5">
                        <span class="block font-semibold text-stone-950">Phone</span>
                        <span class="mt-1 block text-stone-600">+1 (555) 018-2026</span>
                    </div>
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-5">
                        <span class="block font-semibold text-stone-950">Location</span>
                        <span class="mt-1 block text-stone-600">Harbor district</span>
                    </div>
                </div>
            </div>

            <form class="contact-form" method="POST" action="{{ route('contact.store') }}" data-scroll-reveal>
                @csrf

                @if (session('status'))
                    <div class="alert alert-success mb-6" role="status">{{ session('status') }}</div>
                @endif

                <div class="grid gap-5">
                    <fieldset>
                        <legend class="contact-label">Name <span class="required-mark">Required</span></legend>
                        <div class="mt-3 grid gap-4 sm:grid-cols-2">
                            <label>
                                <span class="sr-only">First name</span>
                                <input class="contact-control" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First name" required>
                                @error('first_name')
                                    <span class="field-error mt-2 block">{{ $message }}</span>
                                @enderror
                            </label>
                            <label>
                                <span class="sr-only">Last name</span>
                                <input class="contact-control" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required>
                                @error('last_name')
                                    <span class="field-error mt-2 block">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </fieldset>

                    <label class="contact-label">
                        E-mail <span class="required-mark">Required</span>
                        <input class="contact-control mt-3" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="field-error mt-2 block">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="contact-label">
                        Ask us anything <span class="required-mark">Required</span>
                        <textarea class="contact-control mt-3 min-h-48 resize-y" name="message" maxlength="600" required data-contact-message>{{ old('message') }}</textarea>
                        <span class="mt-3 block text-sm font-medium text-stone-500"><span data-contact-count>{{ strlen(old('message', '')) }}</span> of 600 max characters</span>
                        @error('message')
                            <span class="field-error mt-2 block">{{ $message }}</span>
                        @enderror
                    </label>

                    <button class="btn btn-booking w-fit min-w-28" type="submit">Send</button>
                </div>
            </form>
        </div>
    </section>
@endsection
