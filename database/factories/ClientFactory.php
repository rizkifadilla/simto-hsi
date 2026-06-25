<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),

            'latitude' => -6.200000,
            'longitude' => 106.800000,

            'attendance_radius' => 100,

            'check_in_time' => '08:00:00',
            'check_out_time' => '17:00:00',
        ];
    }
}