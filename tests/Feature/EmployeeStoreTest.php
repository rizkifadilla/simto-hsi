<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_employee()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $client = Client::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->post(route('employees.store'), [
                'email' => 'employee@test.com',
                'password' => 'password123',

                'employee_id' => 'EMP001',
                'full_name' => 'Test Employee',
                'nik_ktp' => '1234567890',
                'phone' => '08123456789',

                'client_id' => $client->id,

                'join_date' => now()->toDateString(),
                'contract_start' => now()->toDateString(),
                'contract_end' => now()->addYear()->toDateString(),
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'employee@test.com'
        ]);

        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP001'
        ]);
    }
}