<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_edit_client_page()
    {
        $admin = User::factory()->create([
            'role'=>'admin'
        ]);

        $client = Client::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->get(route('clients.edit',$client->id));

        $response->assertStatus(200);
    }
}