<?php

namespace App\Http\Controllers;

use App\Support\Availability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityCheckController extends Controller
{
    public function __invoke(Request $request, Availability $availability): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        return response()->json($availability->roomsForDate($validated['date']));
    }
}
