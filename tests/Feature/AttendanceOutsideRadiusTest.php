<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttendanceOutsideRadiusTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_checkin_outside_radius()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $client = Client::factory()->create([
            'latitude' => -6.200000,
            'longitude' => 106.800000,
            'attendance_radius' => 10,
        ]);

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'absent_using_distance' => true,
            'face_descriptor' => json_encode([1,1,1])
        ]);

        $this->actingAs($user);

        $response = $this->post(route('attendance.store'), [
            'photo' => 'data:image/png;base64,'.base64_encode('dummy'),
            'face_descriptor' => json_encode([1,1,1]),
            'latitude' => -7.000000,
            'longitude' => 110.000000,
        ]);

        $response->assertSessionHas('error');
    }
}