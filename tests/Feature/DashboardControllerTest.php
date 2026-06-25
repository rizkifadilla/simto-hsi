<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard-general-dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_with_attendance_data()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $client = Client::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'check_in' => '08:00:00',
            'check_out' => '17:00:00',
            'working_minutes' => 540
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard-general-dashboard');

        $response->assertStatus(200);

        $response->assertViewHas('totalEmployee');
        $response->assertViewHas('presentToday');
        $response->assertViewHas('avgWorkHour');
        $response->assertViewHas('ranking');
    }
}