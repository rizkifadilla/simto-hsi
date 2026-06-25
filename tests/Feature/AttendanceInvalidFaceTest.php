<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class AttendanceInvalidFaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_checkin_with_invalid_face()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $client = Client::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'face_descriptor' => json_encode([0,0,0])
        ]);

        $this->actingAs($user);

        $response = $this->post(route('attendance.store'), [
            'photo' => 'data:image/png;base64,'.base64_encode('dummy'),
            'face_descriptor' => json_encode([10,10,10]),
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $response->assertSessionHas('error');
    }
}