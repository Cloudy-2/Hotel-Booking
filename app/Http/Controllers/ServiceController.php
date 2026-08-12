<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('services.create', [
            'service' => new Service([
                'duration_minutes' => 60,
                'price_cents' => 0,
                'max_guests' => 2,
                'is_active' => true,
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $service = Service::create($this->validated($request));

        return redirect()->route('services.index')->with([
            'feedback_title' => 'Room added',
            'status' => $service->name . ' is now available in room management.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): RedirectResponse
    {
        return redirect()->route('services.edit', $service);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): View
    {
        return view('services.edit', [
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($this->validated($request, $service));

        return redirect()->route('services.index')->with([
            'feedback_title' => 'Room updated',
            'status' => $service->name . ' has been updated across the guest booking experience.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $service->update(['is_active' => false]);

        return redirect()->route('services.index')->with([
            'feedback_title' => 'Room disabled',
            'status' => $service->name . ' is hidden from guest booking pages but remains in your inventory.',
        ]);
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'primary_image' => ['nullable', 'image', 'max:4096'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'max:4096'],
            'amenities' => ['nullable', 'string', 'max:2000'],
            'max_guests' => ['nullable', 'integer', 'min:1', 'max:12'],
            'room_size' => ['nullable', 'string', 'max:80'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_url' => $this->primaryImageUrl($request, $service),
            'gallery' => $this->galleryImageUrls($request, $service),
            'amenities' => $this->linesToArray($validated['amenities'] ?? ''),
            'max_guests' => $validated['max_guests'] ?? 2,
            'room_size' => $validated['room_size'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'price_cents' => (int) round($validated['price'] * 100),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function primaryImageUrl(Request $request, ?Service $service): ?string
    {
        if ($request->hasFile('primary_image')) {
            return Storage::disk('public')->url($request->file('primary_image')->store('rooms', 'public'));
        }

        return $service?->getRawOriginal('image_url');
    }

    private function galleryImageUrls(Request $request, ?Service $service): array
    {
        if ($request->hasFile('gallery_images')) {
            return collect($request->file('gallery_images'))
                ->map(fn ($image) => Storage::disk('public')->url($image->store('rooms/gallery', 'public')))
                ->values()
                ->all();
        }

        $gallery = $service?->getRawOriginal('gallery');

        return $gallery ? (json_decode($gallery, true) ?: []) : [];
    }

    private function linesToArray(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
