<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeDownloadTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_template()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('employees.template'));

        $response->assertStatus(200);

        $response->assertHeader(
            'content-disposition',
            'attachment; filename=employee_template.csv'
        );
    }
}