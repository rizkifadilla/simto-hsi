<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeImportExceptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function import_invalid_csv_format()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $content = "employee_id,name\n";
        $content .= "EMP001,John\n";

        $file = UploadedFile::fake()->createWithContent(
            'employees.csv',
            $content
        );

        $response = $this
            ->actingAs($admin)
            ->post(route('employees.import'), [
                'file' => $file
            ]);

        $response->assertSessionHas('success');
    }
}