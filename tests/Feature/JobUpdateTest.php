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
                'description' => 'Updated Desc',
                'location' => 'Bandung',
                'type' => 'Full Time',
                'requirement' => 'Laravel, PHP',
                'benefit' => 'BPJS, Bonus',
                'salary_min' => 6000000,
                'salary_max' => 9000000,
                'deadline' => now()->addMonth()->format('Y-m-d'),
                'is_active' => 1,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job',
            'location' => 'Bandung',
        ]);
    }
}