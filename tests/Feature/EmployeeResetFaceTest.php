<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeResetFaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_face()
    {
        $admin = User::factory()->create([
            'role'=>'admin'
        ]);

        $client = Client::factory()->create();

        $user = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id'=>$user->id,
            'client_id'=>$client->id,
            'face_descriptor'=>'[1,2,3]'
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('employees.reset-face',$employee->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('employees',[
            'id'=>$employee->id,
            'face_descriptor'=>null
        ]);
    }
}