<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\User;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error(
                'Tidak ada user. Silakan buat user terlebih dahulu.'
            );

            return;
        }

        $jobs = [
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
            ],
        ];

        foreach ($jobs as $job) {

            Job::updateOrCreate(
                [
                    'slug' => $job['slug'],
                ],
                array_merge(
                    $job,
                    [
                        'created_by' => $user->id,
                    ]
                )
            );
        }
    }
}