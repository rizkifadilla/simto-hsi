<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        return [
            'title' => fake()->jobTitle(),
            'slug' => Str::slug(fake()->jobTitle()) . '-' . time(),
            'description' => fake()->paragraph(),
            'requirement' => fake()->paragraph(),
            'benefit' => fake()->paragraph(),
            'location' => 'Jakarta',
            'type' => 'Full Time',
            'salary_min' => 5000000,
            'salary_max' => 10000000,
            'deadline' => now()->addMonth(),
            'is_active' => true,
        ];
    }
}