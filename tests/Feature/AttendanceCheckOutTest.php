<?php

namespace Tests\Feature;

use App\Models\Attendance;
use Tests\TestCase;

class AttendanceCheckOutTest extends TestCase
{
    public function test_employee_can_checkout()
    {
        $data = $this->createEmployeeUser();

        Attendance::create([
            'employee_id' => $data['employee']->id,
            'date' => now()->toDateString(),
            'check_in' => '08:00:00'
        ]);

        $response = $this->actingAs(
            $data['user']
        )->post('/attendance', [
            'photo' => 'data:image/png;base64,aGVsbG8=',
            'face_descriptor' => json_encode([1,2,3]),
            'latitude' => '-6.2',
            'longitude' => '106.8'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'attendances',
            [
                'employee_id' => $data['employee']->id
            ]
        );
    }
}