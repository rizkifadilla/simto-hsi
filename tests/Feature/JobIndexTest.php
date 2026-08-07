<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class JobIndexTest extends TestCase
{
    public function test_admin_can_open_job_index()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('career.index'));

        $response->assertStatus(200);
    }
}