<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_job()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $job = Job::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('career.destroy', $job->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('jobs', [
            'id' => $job->id
        ]);
    }
}