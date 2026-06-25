<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_has_user()
    {
        $employee = Employee::factory()->create();

        $this->assertNotNull(
            $employee->user
        );
    }

    public function test_employee_has_client()
    {
        $employee = Employee::factory()->create();

        $this->assertNotNull(
            $employee->client
        );
    }
}