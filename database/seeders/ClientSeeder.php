<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::insert([
            [
                'name' => 'PT Astra International',
                'address' => 'Jakarta',
                'contact_person' => 'Budi',
                'phone' => '081234567890',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'check_in_time' => '08:00:00',
                'check_out_time' => '17:00:00',
                'attendance_radius' => 100,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Telkom Indonesia',
                'address' => 'Bandung',
                'contact_person' => 'Siti',
                'phone' => '082345678901',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'check_in_time' => '08:00:00',
                'check_out_time' => '17:00:00',
                'attendance_radius' => 150,

                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}