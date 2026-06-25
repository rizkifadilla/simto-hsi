<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeCreatePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_employee_create_page()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Client::create([
            'name' => 'Client A'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('employees.create'));

        $response->assertOk();
    }
}