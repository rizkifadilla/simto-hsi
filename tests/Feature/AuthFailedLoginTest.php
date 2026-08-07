<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthFailedLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_cannot_login_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password'
        ]);

        $response->assertSessionHasErrors();
    }
}