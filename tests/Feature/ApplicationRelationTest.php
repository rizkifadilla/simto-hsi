<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Job;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationRelationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function application_belongs_to_job()
    {
        $application = Application::create([
            'job_id' => Job::create([
                'title' => 'Laravel',
                'description' => 'Desc'
            ])->id,
            'applicant_id' => Applicant::create([
                'name' => 'Test',
                'email' => 'test@mail.com',
                'cv_file' => 'cv.pdf'
            ])->id
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $application->job()
        );
    }

    /** @test */
    public function application_belongs_to_applicant()
    {
        $application = Application::create([
            'job_id' => Job::create([
                'title' => 'Laravel',
                'description' => 'Desc'
            ])->id,
            'applicant_id' => Applicant::create([
                'name' => 'Test',
                'email' => 'test2@mail.com',
                'cv_file' => 'cv.pdf'
            ])->id
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $application->applicant()
        );
    }
}