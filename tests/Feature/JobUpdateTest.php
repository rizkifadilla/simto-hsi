<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_job()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $job = Job::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('career.update', $job->id), [
                'title' => 'Updated Job',
                'description' => 'Updated Desc'
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job'
        ]);
    }
}