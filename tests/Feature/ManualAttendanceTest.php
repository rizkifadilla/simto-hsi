<?php

namespace Tests\Feature;

use App\Models\Attendance;
use Tests\TestCase;

class ManualAttendanceTest extends TestCase
{
    public function test_admin_can_create_manual_attendance()
    {
        $data = $this->createEmployeeUser();

        $admin = $data['user'];

        $admin->update([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($admin)
            ->post('/attendance/manual', [
                'date' => now()->toDateString(),
                'type' => 'izin'
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'attendances',
            [
                'employee_id' => $data['employee']->id,
                'task' => 'izin'
            ]
        );
    }
}