<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            [
                'name' => 'Workspace Consultation',
                'description' => 'A focused session to plan requirements, scope, and next steps.',
                'duration_minutes' => 45,
                'price_cents' => 7500,
            ],
            [
                'name' => 'Implementation Review',
                'description' => 'Review an existing build and leave with prioritized fixes.',
                'duration_minutes' => 60,
                'price_cents' => 12000,
            ],
            [
                'name' => 'Launch Support',
                'description' => 'Hands-on support for go-live preparation and release checks.',
                'duration_minutes' => 90,
                'price_cents' => 18000,
            ],
        ])->each(fn (array $service) => Service::firstOrCreate(
            ['name' => $service['name']],
            $service,
        ));
    }
}
