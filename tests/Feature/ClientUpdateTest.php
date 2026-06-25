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
            'role'=>'admin'
        ]);

        $client = Client::factory()->create([
            'name'=>'Old Client'
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('clients.update',$client->id),[
                'name'=>'New Client'
            ]);

        $response->assertRedirect(
            route('clients.index')
        );

        $this->assertDatabaseHas('clients',[
            'id'=>$client->id,
            'name'=>'New Client'
        ]);
    }
}