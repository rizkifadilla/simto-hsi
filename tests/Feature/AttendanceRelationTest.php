<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_has_attendance()
    {
        $employee = Employee::factory()->create();

        Attendance::factory()->create([
            'employee_id' => $employee->id
        ]);

        $this->assertEquals(
            1,
            $employee->attendances()->count()
        );
    }
}