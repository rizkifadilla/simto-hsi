<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeUpdateAttendanceTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_attendance_time()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $client = Client::factory()->create();

        $user = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id
        ]);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString()
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('attendance.updateTime', $attendance->id), [
                'check_in' => '08:00',
                'check_out' => '16:00'
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'working_minutes' => 480
        ]);
    }
}