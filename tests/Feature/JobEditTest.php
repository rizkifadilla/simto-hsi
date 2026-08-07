<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;

class JobEditTest extends TestCase
{
    public function test_admin_can_open_edit_job()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $job = Job::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('career.edit', $job->id));

        $response->assertStatus(200);
    }
}