<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_url',
        'gallery',
        'amenities',
        'max_guests',
        'room_size',
        'duration_minutes',
        'price_cents',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'duration_minutes' => 'integer',
            'price_cents' => 'integer',
            'gallery' => 'array',
            'amenities' => 'array',
            'max_guests' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price_cents === 0) {
            return 'Free';
        }

        return '$'.number_format($this->price_cents / 100, 2);
    }

    public function getImageUrlAttribute(): string
    {
        if (! empty($this->attributes['image_url'])) {
            return $this->attributes['image_url'];
        }

        return match ($this->id % 3) {
            1 => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
            2 => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80',
            default => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=1200&q=80',
        };
    }

    public function getGalleryAttribute(): array
    {
        $gallery = $this->attributes['gallery'] ?? null;

        if ($gallery) {
            return array_values(array_filter(json_decode($gallery, true) ?: []));
        }

        return [
            $this->image_url,
            'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    public function getAmenitiesAttribute(): array
    {
        $amenities = $this->attributes['amenities'] ?? null;

        if ($amenities) {
            return array_values(array_filter(json_decode($amenities, true) ?: []));
        }

        return match ($this->id % 3) {
            1 => ['King bed', 'City view', 'Fast Wi-Fi', 'Rain shower'],
            2 => ['Balcony', 'Lounge area', 'Work desk', 'Mini bar'],
            default => ['Suite layout', 'Soaking tub', 'Dining nook', 'Late checkout'],
        };
    }
}
