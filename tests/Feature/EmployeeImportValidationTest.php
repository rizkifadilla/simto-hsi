<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeImportValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_requires_file()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('employees.import'), []);

        $response->assertSessionHasErrors('file');
    }
}