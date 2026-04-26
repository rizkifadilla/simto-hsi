<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Applicant;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        Applicant::insert([
            [
                'name' => 'Rizki Fadilla',
                'email' => 'rizki@mail.com',
                'phone' => '08123456789',
                'address' => 'Bandung',
                'cv_file' => 'cv/rizki.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@mail.com',
                'phone' => '08234567890',
                'address' => 'Jakarta',
                'cv_file' => 'cv/budi.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}