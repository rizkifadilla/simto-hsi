<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Client;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // ambil data user & client
        $admin = User::where('email', 'admin@gmail.com')->first();
        $talentacquisition = User::where('email', 'talentacquisition@gmail.com')->first();
        $employee1 = User::where('email', 'employee1@gmail.com')->first();
        $employee2 = User::where('email', 'employee2@gmail.com')->first();

        $client1 = Client::where('name', 'PT Astra International')->first();
        $client2 = Client::where('name', 'PT Telkom Indonesia')->first();

        Employee::insert([
            [
                'user_id' => $admin->id,
                'client_id' => $client1->id,
                'employee_id' => 'EMP001',
                'full_name' => 'Admin System',
                'nik_ktp' => '317xxxxxxxxx',
                'phone' => '0811111111',
                'email' => 'admin@gmail.com',
                'position' => 'Manager',
                'division' => 'IT',
                'placement' => 'HO',
                'join_date' => now(),
                'contract_start' => now(),
                'contract_end' => now()->addYear(),
                'contract_extension_count' => 0,
                'status' => 'active',
                'absent_using_distance' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $talentacquisition->id,
                'client_id' => $client1->id,
                'employee_id' => 'EMP002',
                'full_name' => 'Talent Acquisition',
                'nik_ktp' => '318xxxxxxxxx',
                'phone' => '0822222222',
                'email' => 'talentacquisition@gmail.com',
                'position' => 'Coordinator',
                'division' => 'HRD',
                'placement' => 'HO',
                'join_date' => now(),
                'contract_start' => now(),
                'contract_end' => now()->addMonths(6),
                'contract_extension_count' => 1,
                'status' => 'active',
                'absent_using_distance' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $employee1->id,
                'client_id' => $client2->id,
                'employee_id' => 'EMP003',
                'full_name' => 'Karyawan 1',
                'nik_ktp' => '319xxxxxxxxx',
                'phone' => '0833333333',
                'email' => 'employee1@gmail.com',
                'position' => 'Staff',
                'division' => 'Operational',
                'placement' => 'Warehouse',
                'join_date' => now(),
                'contract_start' => now(),
                'contract_end' => now()->addMonths(3),
                'contract_extension_count' => 0,
                'status' => 'active',
                'absent_using_distance' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $employee2->id,
                'client_id' => $client2->id,
                'employee_id' => 'EMP004',
                'full_name' => 'Karyawan 2',
                'nik_ktp' => '320xxxxxxxxx',
                'phone' => '0844444444',
                'email' => 'employee2@gmail.com',
                'position' => 'Senior Staff',
                'division' => 'Operational',
                'placement' => 'Client',
                'join_date' => now(),
                'contract_start' => now(),
                'contract_end' => now()->addMonths(6),
                'contract_extension_count' => 2,
                'status' => 'active',
                'absent_using_distance' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}