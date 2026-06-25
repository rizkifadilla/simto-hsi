<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_create_employee_page()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('employees.create'));

        $response->assertStatus(200);
    }
}