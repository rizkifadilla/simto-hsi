<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),

            'employee_id' => fake()->unique()->numerify('EMP###'),
            'full_name' => fake()->name(),

            'nik_ktp' => fake()->numerify('################'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),

            'position' => 'Staff',
            'division' => 'IT',
            'placement' => 'Jakarta',

            'join_date' => now()->subYear(),

            'contract_start' => now()->subYear(),

            'contract_end' => now()->addYear(),

            'contract_extension_count' => 0,

            'status' => 'active',

            'absent_using_distance' => false,

            'notes' => null,

            'face_descriptor' => null,
        ];
    }
}