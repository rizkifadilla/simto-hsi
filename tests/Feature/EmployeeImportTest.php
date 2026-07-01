<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_employee_csv()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $client = Client::factory()->create();

        $csvContent =
            "employee_id,full_name,email,role,nik_ktp,phone,client_id,position,division,placement,join_date,contract_start,contract_end,contract_extension_count,status,absent_using_distance,notes\n" .
            "EMP999,Import User,import@test.com,employee,123456789,08123456789,{$client->id},Programmer,IT,Jakarta,2026-01-01,2026-01-01,2026-12-31,0,active,1,Imported";

        $file = UploadedFile::fake()->createWithContent(
            'employee.csv',
            $csvContent
        );

        $response = $this
            ->actingAs($admin)
            ->post(route('employees.import'), [
                'file' => $file
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'import@test.com'
        ]);

        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP999',
            'email' => 'import@test.com'
        ]);
    }
}