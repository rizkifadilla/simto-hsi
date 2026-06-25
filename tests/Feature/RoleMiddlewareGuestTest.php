<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleMiddlewareGuestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_redirected_to_login()
    {
        $response = $this->get('/employees');

        $response->assertRedirect('/login');
    }
}