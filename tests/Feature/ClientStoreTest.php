<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_client()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('clients.store'), [
                'name' => 'PT ABC',
                'address' => 'Jl. Sudirman No. 1 Jakarta',
                'contact_person' => 'Budi',
                'phone' => '081234567890',
                'check_in_time' => '08:00',
                'check_out_time' => '17:00',
            ]);

        $response->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', [
            'name' => 'PT ABC',
            'address' => 'Jl. Sudirman No. 1 Jakarta',
            'contact_person' => 'Budi',
            'phone' => '081234567890',
        ]);
    }
}