<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicApplySuccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function applicant_can_apply_job()
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

        $response = $this->post(
            route('public.apply'),
            [
                'job_id' => $job->id,
                'name' => 'Rizki',
                'email' => 'rizki@test.com',
                'cv' => UploadedFile::fake()->create(
                    'cv.pdf',
                    100,
                    'application/pdf'
                ),
                'g-recaptcha-response' => 'token',
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseCount(
            'applications',
            1
        );
    }
}