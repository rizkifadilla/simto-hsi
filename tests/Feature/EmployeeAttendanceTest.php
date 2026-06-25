<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_employee_attendance()
    {
        $admin = User::factory()->create([
            'role'=>'admin'
        ]);

        $client = Client::factory()->create();

        $user = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id'=>$user->id,
            'client_id'=>$client->id
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('employees.attendance',$employee->id));

        $response->assertStatus(200);
    }
}