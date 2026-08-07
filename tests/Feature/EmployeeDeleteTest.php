<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_employee()
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
            ->delete(route('employees.destroy',$employee->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('employees',[
            'id'=>$employee->id
        ]);
    }
}