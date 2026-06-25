<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    public function definition()
    {
        return [
            'employee_id' => Employee::factory(),
            'date' => now()->toDateString(),
            'check_in' => '08:00:00',
            'check_out' => '17:00:00',
            'working_minutes' => 540,
            'task' => 'Development',
            'is_within_radius' => true,
            'is_face_valid' => true,
        ];
    }
}