<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
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
                'description' => 'Laravel Developer',
                'location' => 'Jakarta',
                'type' => 'Full Time',
                'requirement' => 'Laravel, PHP, MySQL',
                'benefit' => 'BPJS, THR',
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'deadline' => now()->addMonth()->format('Y-m-d'),
                'is_active' => 1,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('jobs', [
            'title' => 'Backend Developer',
            'location' => 'Jakarta',
            'type' => 'Full Time',
        ]);
    }
}