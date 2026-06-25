<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Job;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicApplyDuplicateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function applicant_cannot_apply_twice()
    {
        Storage::fake('public');

        Http::fake([
            '*' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'submit',
            ])
        ]);

        $job = Job::factory()->create();

        $applicant = Applicant::factory()->create([
            'email' => 'rizki@test.com'
        ]);

        Application::factory()->create([
            'job_id' => $job->id,
            'applicant_id' => $applicant->id
        ]);

        $response = $this->post(
            route('public.apply'),
            [
                'job_id' => $job->id,
                'name' => 'Rizki',
                'email' => 'rizki@test.com',
                'cv' => UploadedFile::fake()->create(
                    'cv.pdf'
                ),
                'g-recaptcha-response' => 'token',
            ]
        );

        $response->assertSessionHas('error');
    }
}