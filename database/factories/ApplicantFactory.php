<?php

namespace Database\Factories;

use App\Models\Applicant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantFactory extends Factory
{
    protected $model = Applicant::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'cv_file' => 'cv.pdf',
        ];
    }
}