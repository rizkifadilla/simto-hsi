<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_client()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $client = Client::factory()->create([
            'name' => 'Old Client'
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('clients.update', $client->id), [
                'name' => 'New Client',
                'address' => 'Jl. Gatot Subroto No. 10 Jakarta',
                'contact_person' => 'Andi',
                'phone' => '081298765432',
                'check_in_time' => '08:00',
                'check_out_time' => '17:00',
            ]);

        $response->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'New Client',
            'address' => 'Jl. Gatot Subroto No. 10 Jakarta',
            'contact_person' => 'Andi',
            'phone' => '081298765432',
        ]);
    }
}