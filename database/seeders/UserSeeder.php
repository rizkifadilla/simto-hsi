<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::create([
            'name' => 'Admin System',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'employee_id' => 'EMP001',
            'company' => 'PT Outsource Maju',
            'is_active' => true
        ]);

        // SUPERVISOR
        User::create([
            'name' => 'Supervisor Lapangan',
            'email' => 'supervisor@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
            'employee_id' => 'EMP002',
            'company' => 'PT Outsource Maju',
            'is_active' => true
        ]);

        // EMPLOYEE
        User::create([
            'name' => 'Karyawan 1',
            'email' => 'employee1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'employee_id' => 'EMP003',
            'company' => 'PT Outsource Maju',
            'is_active' => true
        ]);

        User::create([
            'name' => 'Karyawan 2',
            'email' => 'employee2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'employee_id' => 'EMP004',
            'company' => 'PT Outsource Maju',
            'is_active' => true
        ]);
    }
}