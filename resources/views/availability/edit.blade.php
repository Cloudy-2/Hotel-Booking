@extends('layouts.app', [
    'title' => 'Availability | Aurelia Hotel',
    'section' => 'admin',
    'description' => 'Manage Aurelia Hotel weekly reservation hours and holiday closures.',
])

@section('content')
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="mb-5 px-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Management</p>
                <h1 class="mt-2 text-xl font-semibold text-stone-950">Availability</h1>
            </div>
            <nav class="grid gap-1" aria-label="Admin navigation">
                <a class="admin-nav-link" href="{{ route('dashboard') }}">Reservations <span>01</span></a>
                <a class="admin-nav-link" href="{{ route('services.index') }}">Rooms <span>02</span></a>
                <a class="admin-nav-link admin-nav-link-active" href="{{ route('availability.edit') }}">Availability <span>03</span></a>
                <a class="admin-nav-link" href="{{ route('calendar.show') }}">Calendar <span>04</span></a>
            </nav>
        </aside>

        <section class="min-w-0 p-4 sm:p-6 lg:p-8">
            <div class="dashboard-hero mb-6">
                <div>
                    <p class="section-kicker">Scheduling</p>
                    <h1 class="page-title mt-3">Availability Rules</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-600">Control when guests can select arrival times. These rules power the availability modal and reservation form.</p>
                </div>
                <a class="btn btn-secondary" href="{{ route('calendar.show') }}">View Calendar</a>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1fr_380px] xl:items-start">
                <section class="panel overflow-hidden">
                    <div class="panel-header">
                        <div>
                            <h2 class="text-xl font-semibold text-stone-950">Weekly hours</h2>
                            <p class="mt-2 text-sm text-stone-600">Set one operating window per weekday. Closed days will reject all guest arrival times.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('availability.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="overflow-x-auto">
                            <table class="data-table responsive-table">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Open</th>
                                        <th>Close</th>
                                        <th>Closed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($weekdays as $weekday => $label)
                                        @php
                                            $rule = $rules->get($weekday);
                                            $isClosed = old("rules.$weekday.is_closed", $rule?->is_closed ?? false);
                                        @endphp
                                        <tr>
                                            <td data-label="Day">
                                                <strong class="font-semibold text-stone-950">{{ $label }}</strong>
                                            </td>
                                            <td data-label="Open">
                                                <input class="field-control" type="time" name="rules[{{ $weekday }}][opens_at]" value="{{ old("rules.$weekday.opens_at", $rule?->opens_at ? substr($rule->opens_at, 0, 5) : '08:00') }}">
                                                @error("rules.$weekday.opens_at") <span class="field-error">{{ $message }}</span> @enderror
                                            </td>
                                            <td data-label="Close">
                                                <input class="field-control" type="time" name="rules[{{ $weekday }}][closes_at]" value="{{ old("rules.$weekday.closes_at", $rule?->closes_at ? substr($rule->closes_at, 0, 5) : '22:00') }}">
                                                @error("rules.$weekday.closes_at") <span class="field-error">{{ $message }}</span> @enderror
                                            </td>
                                            <td data-label="Closed">
                                                <label class="inline-flex items-center gap-2 text-sm font-semibold text-stone-700">
                                                    <input type="checkbox" name="rules[{{ $weekday }}][is_closed]" value="1" class="size-4 rounded border-stone-300" @checked($isClosed)>
                                                    Closed
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-stone-200 p-5">
                            <button class="btn btn-primary" type="submit">Save Weekly Hours</button>
                        </div>
                    </form>
                </section>

                <aside class="grid gap-6">
                    <section class="panel">
                        <div class="panel-header">
                            <div>
                                <h2 class="text-xl font-semibold text-stone-950">Holiday closure</h2>
                                <p class="mt-2 text-sm text-stone-600">Add dates where normal weekly hours should not apply.</p>
                            </div>
                        </div>
                        <form class="grid gap-4 p-5" method="POST" action="{{ route('holidays.store') }}">
                            @csrf
                            <label class="field-label">
                                Date
                                <input class="field-control" type="date" name="date" required>
                                @error('date') <span class="field-error">{{ $message }}</span> @enderror
                            </label>
                            <label class="field-label">
                                Name
                                <input class="field-control" name="name" maxlength="160" placeholder="Maintenance day" required>
                                @error('name') <span class="field-error">{{ $message }}</span> @enderror
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-stone-900">
                                <input type="checkbox" name="is_closed" value="1" class="size-4 rounded border-stone-300" checked>
                                Closed for reservations
                            </label>
                            <button class="btn btn-primary" type="submit">Add Closure</button>
                        </form>
                    </section>

                    <section class="panel overflow-hidden">
                        <div class="panel-header">
                            <div>
                                <h2 class="text-xl font-semibold text-stone-950">Closure dates</h2>
                                <p class="mt-2 text-sm text-stone-600">Dates currently affecting guest availability.</p>
                            </div>
                        </div>
                        @forelse ($holidays as $holiday)
                            <div class="flex items-center justify-between gap-4 border-t border-stone-100 p-4 first:border-t-0">
                                <div>
                                    <strong class="block text-stone-950">{{ $holiday->name }}</strong>
                                    <span class="text-sm text-stone-500">{{ $holiday->date->format('M j, Y') }}</span>
                                </div>
                                <form method="POST" action="{{ route('holidays.destroy', $holiday) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger min-h-9 px-3 py-1.5" type="submit">Remove</button>
                                </form>
                            </div>
                        @empty
                            <div class="empty-state">
                                <h3 class="text-lg font-semibold text-stone-950">No closures yet</h3>
                                <p class="mt-2 text-sm text-stone-600">Add holiday or maintenance dates as needed.</p>
                            </div>
                        @endforelse
                    </section>
                </aside>
            </div>
        </section>
    </div>
@endsection
