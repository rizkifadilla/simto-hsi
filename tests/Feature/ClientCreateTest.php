<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_create_client_page()
    {
        $admin = User::factory()->create([
            'role'=>'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('clients.create'));

        $response->assertStatus(200);
    }
}