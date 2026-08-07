<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedirectAuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logged_in_user_redirected_from_login_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/login');

        $response->assertRedirect(
            RouteServiceProvider::HOME
        );
    }
}