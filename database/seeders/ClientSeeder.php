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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Telkom Indonesia',
                'address' => 'Bandung',
                'contact_person' => 'Siti',
                'phone' => '082345678901',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}