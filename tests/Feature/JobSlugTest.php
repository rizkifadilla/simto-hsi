<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobSlugTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function slug_is_generated_automatically()
    {
        $job = Job::create([
            'title' => 'Senior Laravel Developer',
            'description' => 'Job Description'
        ]);

        $this->assertNotNull($job->slug);

        $this->assertStringContainsString(
            'senior-laravel-developer',
            $job->slug
        );
    }
}