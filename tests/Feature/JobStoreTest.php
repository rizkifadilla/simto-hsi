<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_job()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('career.store'), [
                'title' => 'Backend Developer',
                'description' => 'Laravel Developer'
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'title' => 'Backend Developer'
        ]);
    }
}