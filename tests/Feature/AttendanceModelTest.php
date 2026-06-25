<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function attendance_belongs_to_employee()
    {
        $user = User::factory()->create();

        $client = Client::create([
            'name' => 'Client A'
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'employee_id' => 'EMP001',
            'full_name' => 'Employee',
            'join_date' => now(),
            'contract_start' => now(),
            'contract_end' => now()->addYear(),
        ]);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->format('Y-m-d')
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $attendance->employee()
        );
    }
}