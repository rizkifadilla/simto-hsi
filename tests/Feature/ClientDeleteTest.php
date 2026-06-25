<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_client()
    {
        $admin = User::factory()->create([
            'role'=>'admin'
        ]);

        $client = Client::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->delete(route('clients.destroy',$client->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('clients',[
            'id'=>$client->id
        ]);
    }
}