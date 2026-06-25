<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition()
    {
        return [
            'job_id' => Job::factory(),
            'applicant_id' => Applicant::factory(),
            'cover_letter' => fake()->paragraph(),
            'is_followed_up' => false,
            'followed_up_at' => null,
            'notes' => null,
        ];
    }
}