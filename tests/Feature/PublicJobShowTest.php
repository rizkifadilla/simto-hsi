<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicJobShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_can_view_job_detail()
    {
        $job = Job::factory()->create([
            'slug' => 'laravel-dev'
        ]);

        $response = $this->get(
            route('public.show', $job->slug)
        );

        $response->assertStatus(200);
    }
}