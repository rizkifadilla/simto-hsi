<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_employee_index()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('employees.index'));

        $response->assertStatus(200);
    }
}