<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyAttendanceTest extends TestCase
{
    public function test_employee_can_open_my_attendance()
    {
        $data = $this->createEmployeeUser();

        $response = $this->actingAs(
            $data['user']
        )->get('/my-attendance');

        $response->assertStatus(200);
    }
}