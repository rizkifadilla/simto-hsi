<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeDistanceSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_distance_setting()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('employees.distance-setting'));

        $response->assertStatus(200);
    }
}