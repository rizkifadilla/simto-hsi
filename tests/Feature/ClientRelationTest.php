<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientRelationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function client_has_many_employees()
    {
        $client = Client::create([
            'name' => 'Client A'
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $client->employees()
        );
    }
}