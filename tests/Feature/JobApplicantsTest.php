<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;

class JobApplicantsTest extends TestCase
{
    public function test_admin_can_view_applicants()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $job = Job::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('career.applicants', $job->id));

        $response->assertStatus(200);
    }
}