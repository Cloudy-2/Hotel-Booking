<?php

namespace Database\Seeders;

use App\Models\AvailabilityRule;
use App\Models\Holiday;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@aurelia.test'],
            [
                'name' => 'Aurelia Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ],
        );

        User::firstOrCreate(
            ['email' => 'guest@aurelia.test'],
            [
                'name' => 'Guest Customer',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
            ],
        );

        collect(range(0, 6))->each(fn (int $weekday) => AvailabilityRule::updateOrCreate(
            ['weekday' => $weekday],
            [
                'opens_at' => in_array($weekday, [0, 6], true) ? '10:00:00' : '08:00:00',
                'closes_at' => in_array($weekday, [0, 6], true) ? '18:00:00' : '22:00:00',
                'is_closed' => false,
            ],
        ));

        Holiday::firstOrCreate(
            ['date' => '2026-12-25'],
            ['name' => 'Christmas Day', 'is_closed' => true],
        );

        collect([
            [
                'name' => 'Workspace Consultation',
                'description' => 'A focused session to plan requirements, scope, and next steps.',
                'image_url' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
                ],
                'amenities' => ['King bed', 'City view', 'Fast Wi-Fi', 'Rain shower'],
                'max_guests' => 2,
                'room_size' => '32 sqm',
                'duration_minutes' => 45,
                'price_cents' => 7500,
            ],
            [
                'name' => 'Implementation Review',
                'description' => 'Review an existing build and leave with prioritized fixes.',
                'image_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
                ],
                'amenities' => ['Balcony', 'Lounge area', 'Work desk', 'Mini bar'],
                'max_guests' => 3,
                'room_size' => '44 sqm',
                'duration_minutes' => 60,
                'price_cents' => 12000,
            ],
            [
                'name' => 'Launch Support',
                'description' => 'Hands-on support for go-live preparation and release checks.',
                'image_url' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=1200&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
                ],
                'amenities' => ['Suite layout', 'Soaking tub', 'Dining nook', 'Late checkout'],
                'max_guests' => 4,
                'room_size' => '58 sqm',
                'duration_minutes' => 90,
                'price_cents' => 18000,
            ],
        ])->each(fn (array $service) => Service::updateOrCreate(
            ['name' => $service['name']],
            $service,
        ));
    }
}
