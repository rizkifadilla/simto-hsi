<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobUpdateApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_application()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $application = Application::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(
                route('career.application.update', $application->id),
                [
                    'notes' => 'Interview Scheduled',
                    'follow_up' => true
                ]
            );

        $response->assertRedirect();

        $application->refresh();

        $this->assertEquals(
            'Interview Scheduled',
            $application->notes
        );

        $this->assertNotNull(
            $application->followed_up_at
        );
    }
}