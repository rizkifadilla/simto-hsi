<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Job;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicApplyBotDetectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function application_rejected_when_recaptcha_failed()
    {
        Storage::fake('public');

        Http::fake([
            '*' => Http::response([
                'success' => false,
                'score' => 0.1,
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
                    'cv.pdf'
                ),
                'g-recaptcha-response' => 'token',
            ]
        );

        $response->assertSessionHas('error');
    }
}