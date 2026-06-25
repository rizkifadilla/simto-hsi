<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleMiddlewareForbiddenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function employee_cannot_access_admin_page()
    {
        $user = User::factory()->create([
            'role' => 'employee'
        ]);

        $this->actingAs($user);

        $response = $this->get('/employees');

        $response->assertStatus(403);
    }
}