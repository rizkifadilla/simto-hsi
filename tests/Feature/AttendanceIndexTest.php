<?php

namespace Tests\Feature;

use Tests\TestCase;

class AttendanceIndexTest extends TestCase
{
    public function test_employee_can_open_attendance_page()
    {
        $data = $this->createEmployeeUser();

        $response = $this->actingAs(
            $data['user']
        )->get('/attendance');

        $response->assertStatus(200);
    }
}