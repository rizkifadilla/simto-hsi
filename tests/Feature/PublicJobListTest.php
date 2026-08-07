<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicJobListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_can_view_job_list()
    {
        Job::factory()->count(3)->create();

        $response = $this->get(route('public.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function public_can_search_job()
    {
        Job::factory()->create([
            'title' => 'Laravel Developer'
        ]);

        $response = $this->get('/career-public?search=Laravel');

        $response->assertStatus(200);
        $response->assertSee('Laravel');
    }
}