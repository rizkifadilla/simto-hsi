<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_employee()
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
            ->put(route('employees.update',$employee->id),[
                'full_name'=>'Updated Employee',
                'email'=>'updated@test.com',
                'employee_id'=>$employee->employee_id,
                'nik_ktp'=>$employee->nik_ktp,
                'role'=>'employee'
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('employees',[
            'id'=>$employee->id,
            'full_name'=>'Updated Employee'
        ]);
    }
}