<?php

namespace Tests;

use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createEmployeeUser()
    {
        $user = User::factory()->create([
            'role' => 'employee'
        ]);

        $client = Client::factory()->create();

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'absent_using_distance' => false,
        ]);

        return compact(
            'user',
            'employee',
            'client'
        );
    }
}