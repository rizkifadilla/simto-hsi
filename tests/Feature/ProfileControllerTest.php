<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_can_be_opened()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertStatus(200);
    }

    public function test_user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword')
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile/password', [
                'current_password' => 'oldpassword',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword'
            ]);

        $response->assertSessionHas('success');

        $this->assertTrue(
            Hash::check(
                'newpassword',
                $user->fresh()->password
            )
        );
    }

    public function test_change_password_fail_when_old_password_wrong()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword')
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile/password', [
                'current_password' => 'SALAH',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword'
            ]);

        $response->assertSessionHasErrors(
            'current_password'
        );
    }
}