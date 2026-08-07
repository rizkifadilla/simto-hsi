<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceExportMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_monitoring()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.monitoring.export'));

        $response->assertStatus(200);

        $response->assertHeader(
            'content-type',
            'application/vnd.ms-excel; charset=UTF-8'
        );
    }
}