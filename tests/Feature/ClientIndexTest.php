<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_client_index()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('clients.index'));

        $response->assertStatus(200);
    }
}