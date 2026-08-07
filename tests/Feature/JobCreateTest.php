<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class JobCreateTest extends TestCase
{
    public function test_admin_can_open_create_job_page()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('career.create'));

        $response->assertStatus(200);
    }
}