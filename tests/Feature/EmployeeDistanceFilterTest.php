<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeDistanceFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_distance_setting_with_filters()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('employees.distance-setting', [
                'client_id' => 1,
                'division' => 'IT'
            ]));

        $response->assertOk();
    }
}