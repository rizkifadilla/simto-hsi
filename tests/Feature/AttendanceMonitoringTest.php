<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_monitoring_page()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.monitoring'));

        $response->assertStatus(200);
    }
}