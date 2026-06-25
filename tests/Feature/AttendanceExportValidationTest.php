<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceExportValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_requires_valid_date_range()
    {
        $user = User::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.export', [
            'from' => '2025-01-10',
            'to' => '2025-01-01'
        ]));

        $response->assertSessionHasErrors('to');
    }
}