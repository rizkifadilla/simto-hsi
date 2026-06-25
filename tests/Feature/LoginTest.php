<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_opened()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }
}