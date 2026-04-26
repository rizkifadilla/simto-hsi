<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Job;
use App\Models\Applicant;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $job = Job::first();
        $applicant = Applicant::first();

        Application::create([
            'job_id' => $job->id,
            'applicant_id' => $applicant->id,
            'cover_letter' => 'Saya tertarik dengan posisi ini.',
            'is_followed_up' => false,
            'notes' => null,
        ]);
    }
}