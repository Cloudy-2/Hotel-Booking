<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityRule;
use App\Models\Holiday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function edit(): View
    {
        return view('availability.edit', [
            'rules' => AvailabilityRule::orderBy('weekday')->get()->keyBy('weekday'),
            'holidays' => Holiday::orderByDesc('date')->get(),
            'weekdays' => $this->weekdays(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rules' => ['required', 'array', 'size:7'],
            'rules.*.opens_at' => ['nullable', 'date_format:H:i'],
            'rules.*.closes_at' => ['nullable', 'date_format:H:i'],
            'rules.*.is_closed' => ['nullable', 'boolean'],
        ]);

        foreach ($this->weekdays() as $weekday => $label) {
            $rule = $validated['rules'][$weekday] ?? [];
            $isClosed = filter_var($rule['is_closed'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if (! $isClosed && (empty($rule['opens_at']) || empty($rule['closes_at']) || $rule['opens_at'] >= $rule['closes_at'])) {
                return back()
                    ->withErrors(["rules.{$weekday}.opens_at" => "{$label} must have valid opening and closing times."])
                    ->withInput();
            }

            AvailabilityRule::updateOrCreate(
                ['weekday' => $weekday],
                [
                    'opens_at' => $isClosed ? null : $rule['opens_at'],
                    'closes_at' => $isClosed ? null : $rule['closes_at'],
                    'is_closed' => $isClosed,
                ],
            );
        }

        return back()->with([
            'feedback_title' => 'Availability updated',
            'status' => 'Weekly reservation hours have been saved.',
        ]);
    }

    public function storeHoliday(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:160'],
            'is_closed' => ['nullable', 'boolean'],
        ]);

        Holiday::updateOrCreate(
            ['date' => $validated['date']],
            [
                'name' => $validated['name'],
                'is_closed' => $request->boolean('is_closed', true),
            ],
        );

        return back()->with([
            'feedback_title' => 'Holiday saved',
            'status' => 'The closure date is now used by availability checks.',
        ]);
    }

    public function destroyHoliday(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return back()->with([
            'feedback_title' => 'Holiday removed',
            'status' => 'The date is available according to weekly hours again.',
        ]);
    }

    private function weekdays(): array
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }
}
