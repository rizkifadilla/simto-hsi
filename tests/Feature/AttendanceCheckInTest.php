<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceCheckInTest extends TestCase
{
    public function test_employee_can_checkin()
    {
        Storage::fake('public');

        $data = $this->createEmployeeUser();

        $response = $this->actingAs(
            $data['user']
        )->post('/attendance', [
            'photo' => 'data:image/png;base64,aGVsbG8=',
            'face_descriptor' => json_encode([1,2,3]),
            'latitude' => '-6.2',
            'longitude' => '106.8'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'attendances',
            [
                'employee_id' => $data['employee']->id
            ]
        );
    }
}