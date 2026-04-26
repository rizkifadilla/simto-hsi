<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        Job::insert([
            [
                'title' => 'Backend Developer',
                'slug' => 'backend-developer',
                'description' => 'Mengembangkan API dan sistem backend.',
                'requirement' => 'Laravel, MySQL, REST API',
                'benefit' => 'Gaji kompetitif, BPJS, Bonus',
                'location' => 'Bandung',
                'type' => 'Fulltime',
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'deadline' => now()->addDays(30),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Frontend Developer',
                'slug' => 'frontend-developer',
                'description' => 'Membuat UI modern.',
                'requirement' => 'Vue / React / JS',
                'benefit' => 'Remote, Bonus',
                'location' => 'Jakarta',
                'type' => 'Fulltime',
                'salary_min' => 4000000,
                'salary_max' => 7000000,
                'deadline' => now()->addDays(20),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}