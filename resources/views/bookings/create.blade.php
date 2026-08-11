@extends('layouts.app', ['title' => 'Reserve a Room | Aurelia Hotel', 'section' => 'guest'])

@section('content')
    <section class="border-b border-stone-200 bg-white">
        <div class="page-section py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="section-kicker">Reservation</p>
                    <h1 class="page-title mt-3">Complete your booking request</h1>
                    <p class="lede mt-3">Share the essentials and the reservations team will review availability before confirming your stay.</p>
                </div>
                <ol class="flex flex-wrap gap-2 text-xs font-semibold text-stone-500" aria-label="Booking progress">
                    <li class="rounded-full bg-stone-950 px-3 py-1.5 text-white">Room</li>
                    <li class="rounded-full border border-stone-200 px-3 py-1.5">Guest Details</li>
                    <li class="rounded-full border border-stone-200 px-3 py-1.5">Review</li>
                    <li class="rounded-full border border-stone-200 px-3 py-1.5">Confirmation</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="page-section">
        <form method="POST" action="{{ route('bookings.store') }}" class="grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
            @csrf

            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="section-title text-xl">Guest and stay details</h2>
                        <p class="mt-2 text-sm text-stone-600">Required fields are marked by the browser and validated by the reservation system.</p>
                    </div>
                </div>

                <div class="grid gap-5 p-5 sm:grid-cols-2">
                    <label class="field-label sm:col-span-2">
                        Room experience
                        <select class="field-control" name="service_id" required>
                            <option value="">Select a room</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @selected(old('service_id', request('service_id')) == $service->id)>
                                    {{ $service->name }} | {{ $service->duration_minutes }} min | {{ $service->formatted_price }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Full name
                        <input class="field-control" name="customer_name" value="{{ old('customer_name') }}" maxlength="120" autocomplete="name" required>
                        @error('customer_name') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Email address
                        <input class="field-control" type="email" name="customer_email" value="{{ old('customer_email') }}" maxlength="180" autocomplete="email" required>
                        @error('customer_email') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Phone
                        <input class="field-control" type="tel" name="customer_phone" value="{{ old('customer_phone') }}" maxlength="40" autocomplete="tel">
                        @error('customer_phone') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label">
                        Arrival time
                        <input class="field-control" type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required>
                        @error('starts_at') <span class="field-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="field-label sm:col-span-2">
                        Requests or arrival notes
                        <textarea class="field-control min-h-32" name="notes" maxlength="1000" placeholder="Airport transfer, accessibility needs, late arrival, or special occasion">{{ old('notes') }}</textarea>
                        @error('notes') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>

            <aside class="panel lg:sticky lg:top-24" aria-label="Booking summary">
                <div class="p-5">
                    <p class="section-kicker">Summary</p>
                    <h2 class="mt-3 text-xl font-semibold text-stone-950">Your request</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-3">
                            <dt class="text-stone-500">Room</dt>
                            <dd class="text-right font-medium text-stone-950">Selected in form</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-3">
                            <dt class="text-stone-500">Status</dt>
                            <dd><span class="badge badge-pending">Pending review</span></dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 border-b border-stone-100 pb-3">
                            <dt class="text-stone-500">Rate</dt>
                            <dd class="text-right font-medium text-stone-950">Shown by room</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-stone-500">Next step</dt>
                            <dd class="max-w-44 text-right font-medium text-stone-950">Team confirms availability</dd>
                        </div>
                    </dl>

                    <div class="mt-6 grid gap-3">
                        <button type="submit" class="btn btn-primary w-full">Submit Request</button>
                        <a class="btn btn-secondary w-full" href="{{ route('home') }}">Back to Rooms</a>
                    </div>
                </div>
            </aside>
        </form>
    </section>
@endsection
