<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_area(): void
    {
        $this->get('/admin/reservations')
            ->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_admin_area(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/admin/reservations')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/reservations')
            ->assertOk()
            ->assertSee('Booking Dashboard');
    }
}
