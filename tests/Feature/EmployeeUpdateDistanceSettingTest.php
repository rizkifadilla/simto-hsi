<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeUpdateDistanceSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_distance_setting()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $client = Client::factory()->create();

        $user = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'absent_using_distance' => false
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('employees.distance-setting.update'), [
                'employee_ids' => [$employee->id]
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'absent_using_distance' => true
        ]);
    }
}