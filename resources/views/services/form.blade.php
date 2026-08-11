@csrf

@php
    $storedGallery = $service->getRawOriginal('gallery');
    $storedAmenities = $service->getRawOriginal('amenities');
    $amenityLines = old('amenities', $storedAmenities ? implode(PHP_EOL, json_decode($storedAmenities, true) ?: []) : '');
    $previewImage = $service->image_url;
    $previewName = old('name', $service->name ?: 'Room name');
    $previewDescription = old('description', $service->description ?: 'A short guest-facing description will appear here.');
    $previewRate = old('price', number_format(($service->price_cents ?? 0) / 100, 2, '.', ''));
    $previewGuests = old('max_guests', $service->max_guests ?? 2);
    $previewSize = old('room_size', $service->room_size ?: 'Size not set');
@endphp

<div class="grid gap-6 xl:grid-cols-[1fr_360px] xl:items-start">
    <div class="grid gap-5">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Guest-facing basics</h2>
                    <p class="mt-2 text-sm text-stone-600">This is the core information customers see while comparing rooms.</p>
                </div>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2">
                <label class="field-label sm:col-span-2">
                    Room name
                    <input class="field-control" name="name" value="{{ old('name', $service->name) }}" maxlength="160" placeholder="Harbor Suite" required data-service-name>
                    <span class="text-xs font-medium text-stone-500">Use a short, distinctive name. Avoid internal codes.</span>
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="field-label sm:col-span-2">
                    Room description
                    <textarea class="field-control min-h-32" name="description" maxlength="1000" placeholder="A calm suite with water views, lounge seating, and a generous work area." data-service-description>{{ old('description', $service->description) }}</textarea>
                    <span class="text-xs font-medium text-stone-500">Describe the guest benefit in one or two sentences.</span>
                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Photos</h2>
                    <p class="mt-2 text-sm text-stone-600">Use strong landscape images. The first image becomes the main room thumbnail.</p>
                </div>
            </div>

            <div class="grid gap-5 p-5">
                <div class="grid gap-3">
                    <span class="field-label">Primary room photo</span>
                    <label class="upload-dropzone" data-upload-dropzone>
                        <input class="sr-only" type="file" name="primary_image" accept="image/*" data-service-image data-upload-input>
                        <span class="upload-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 16V4m0 0 4.5 4.5M12 4 7.5 8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.75 15.25v2.5A2.25 2.25 0 0 0 7 20h10a2.25 2.25 0 0 0 2.25-2.25v-2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-stone-950">Drop the main room photo here</span>
                            <span class="mt-1 block text-sm text-stone-500" data-upload-label>PNG, JPG, or WEBP up to 4 MB</span>
                        </span>
                    </label>
                    @if ($service->exists)
                        <p class="text-xs font-medium text-stone-500">Current photo stays unchanged unless you drop a new one.</p>
                    @endif
                    @error('primary_image') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="grid gap-3">
                    <span class="field-label">Gallery photos</span>
                    <label class="upload-dropzone" data-upload-dropzone>
                        <input class="sr-only" type="file" name="gallery_images[]" accept="image/*" multiple data-service-gallery data-upload-input>
                        <span class="upload-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M5 8.75A2.75 2.75 0 0 1 7.75 6h8.5A2.75 2.75 0 0 1 19 8.75v6.5A2.75 2.75 0 0 1 16.25 18h-8.5A2.75 2.75 0 0 1 5 15.25v-6.5Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="m8 15 2.3-2.3a1 1 0 0 1 1.4 0l1.05 1.05.8-.8a1 1 0 0 1 1.4 0L17 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.5 6V5A2 2 0 0 1 10.5 3h7A3.5 3.5 0 0 1 21 6.5v6a2 2 0 0 1-2 2h-.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-stone-950">Drop gallery photos here</span>
                            <span class="mt-1 block text-sm text-stone-500" data-upload-label>Drop multiple images to replace the room carousel</span>
                        </span>
                    </label>
                    @if ($service->exists)
                        <div class="flex flex-wrap gap-2">
                            @foreach ($service->gallery as $imageUrl)
                                <img src="{{ $imageUrl }}" alt="" class="h-14 w-20 rounded-md object-cover">
                            @endforeach
                        </div>
                        <p class="text-xs font-medium text-stone-500">Current gallery stays unchanged unless you drop new gallery photos.</p>
                    @endif
                    @error('gallery_images') <span class="field-error">{{ $message }}</span> @enderror
                    @error('gallery_images.*') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Booking details</h2>
                    <p class="mt-2 text-sm text-stone-600">These details affect availability, pricing, and the comparison table.</p>
                </div>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2">
                <label class="field-label">
                    Reservation window
                    <input class="field-control" type="number" name="duration_minutes" min="15" max="1440" value="{{ old('duration_minutes', $service->duration_minutes) }}" required data-service-window>
                    <span class="text-xs font-medium text-stone-500">How long the booking blocks availability, in minutes.</span>
                    @error('duration_minutes') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="field-label">
                    Nightly rate
                    <input class="field-control" type="number" name="price" min="0" step="0.01" value="{{ $previewRate }}" required data-service-rate>
                    <span class="text-xs font-medium text-stone-500">Enter the public price, for example 250.00.</span>
                    @error('price') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="field-label">
                    Guest capacity
                    <input class="field-control" type="number" name="max_guests" min="1" max="12" value="{{ $previewGuests }}" required data-service-guests>
                    @error('max_guests') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="field-label">
                    Room size
                    <input class="field-control" name="room_size" value="{{ old('room_size', $service->room_size) }}" maxlength="80" placeholder="32 sqm" data-service-size>
                    @error('room_size') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Amenities and publishing</h2>
                    <p class="mt-2 text-sm text-stone-600">Keep amenities short so they scan well on room cards and availability results.</p>
                </div>
            </div>

            <div class="grid gap-5 p-5">
                <label class="field-label">
                    Amenities
                    <textarea class="field-control min-h-28" name="amenities" maxlength="2000" placeholder="King bed&#10;City view&#10;Fast Wi-Fi&#10;Rain shower" data-service-amenities>{{ $amenityLines }}</textarea>
                    <span class="text-xs font-medium text-stone-500">Add one amenity per line. Aim for 4 to 6 high-value details.</span>
                    @error('amenities') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="flex items-start gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm font-semibold text-stone-900">
                    <input type="checkbox" name="is_active" value="1" class="mt-0.5 size-4 rounded border-stone-300" @checked(old('is_active', $service->is_active ?? true))>
                    <span>
                        Active on guest site
                        <span class="mt-1 block text-xs font-medium leading-5 text-stone-500">Turn this off to hide the room from customers without deleting it.</span>
                    </span>
                </label>
            </div>
        </section>

        <div class="flex flex-wrap gap-3">
            <button class="btn btn-primary" type="submit">{{ $buttonLabel }}</button>
            <a class="btn btn-secondary" href="{{ route('services.index') }}">Cancel</a>
        </div>
    </div>

    <aside class="panel overflow-hidden xl:sticky xl:top-24" aria-label="Room listing preview">
        <div class="border-b border-stone-200 p-5">
            <p class="section-kicker">Preview</p>
            <h2 class="mt-3 text-xl font-semibold text-stone-950">Guest room card</h2>
            <p class="mt-2 text-sm text-stone-600">This updates while you edit the form.</p>
        </div>
        <img src="{{ $previewImage }}" alt="" class="h-52 w-full object-cover" data-service-preview-image data-default-image="{{ $service->image_url }}">
        <div class="p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-stone-950" data-service-preview-name>{{ $previewName }}</h3>
                    <p class="mt-1 text-sm text-stone-500"><span data-service-preview-window>{{ old('duration_minutes', $service->duration_minutes) ?: 60 }}</span> min reservation window</p>
                </div>
                <span class="whitespace-nowrap text-sm font-semibold text-stone-950" data-service-preview-rate>${{ number_format((float) $previewRate, 2) }}</span>
            </div>
            <p class="mt-4 text-sm leading-6 text-stone-600" data-service-preview-description>{{ $previewDescription }}</p>
            <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-md border border-stone-200 p-3">
                    <dt class="text-stone-500">Guests</dt>
                    <dd class="mt-1 font-semibold text-stone-950">Up to <span data-service-preview-guests>{{ $previewGuests }}</span></dd>
                </div>
                <div class="rounded-md border border-stone-200 p-3">
                    <dt class="text-stone-500">Size</dt>
                    <dd class="mt-1 font-semibold text-stone-950" data-service-preview-size>{{ $previewSize }}</dd>
                </div>
            </dl>
            <div class="mt-5">
                <p class="text-sm font-semibold text-stone-950">Amenities</p>
                <div class="mt-3 flex flex-wrap gap-2" data-service-preview-amenities>
                    @forelse (($amenityLines ? preg_split('/\r\n|\r|\n/', $amenityLines) : $service->amenities) as $amenity)
                        @if (trim($amenity) !== '')
                            <span class="rounded-full border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600">{{ trim($amenity) }}</span>
                        @endif
                    @empty
                        <span class="text-sm text-stone-500">Amenities will appear here.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </aside>
</div>
