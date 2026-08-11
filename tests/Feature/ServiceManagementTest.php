<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_service(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/services', [
            'name' => 'Harbor Suite',
            'description' => 'A calm room with water views.',
            'duration_minutes' => 90,
            'price' => 250.50,
            'is_active' => '1',
        ])->assertRedirect(route('services.index'));

        $this->assertDatabaseHas('services', [
            'name' => 'Harbor Suite',
            'price_cents' => 25050,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_upload_room_photos(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/services', [
            'name' => 'Gallery Suite',
            'description' => 'A room with a complete photo set.',
            'duration_minutes' => 90,
            'price' => 320,
            'primary_image' => $this->fakePng('primary.png'),
            'gallery_images' => [
                $this->fakePng('angle-one.png'),
                $this->fakePng('angle-two.png'),
            ],
            'is_active' => '1',
        ])->assertRedirect(route('services.index'));

        $service = Service::where('name', 'Gallery Suite')->firstOrFail();

        $this->assertStringContainsString('/storage/rooms/', $service->getRawOriginal('image_url'));
        $this->assertCount(2, json_decode($service->getRawOriginal('gallery'), true));
    }

    public function test_admin_can_disable_service(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::create(['name' => 'Garden Room', 'duration_minutes' => 60, 'price_cents' => 15000, 'is_active' => true]);

        $this->actingAs($admin)
            ->delete(route('services.destroy', $service))
            ->assertRedirect(route('services.index'));

        $this->assertFalse($service->fresh()->is_active);
    }

    private function fakePng(string $name): UploadedFile
    {
        $directory = storage_path('framework/testing-temp/uploads');

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $path = $directory.'/'.$name;
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
