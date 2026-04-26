<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        foreach ($employees as $emp) {

            // generate 30 hari ke belakang
            for ($i = 0; $i < 30; $i++) {

                $date = Carbon::now()->subDays($i);

                // skip random biar ada yang tidak hadir
                if (rand(1, 10) <= 2) {
                    continue;
                }

                $checkIn = Carbon::parse($date->format('Y-m-d') . ' 08:' . rand(0, 30));
                $checkOut = Carbon::parse($date->format('Y-m-d') . ' 17:' . rand(0, 30));

                $workingMinutes = $checkOut->diffInMinutes($checkIn);

                Attendance::create([
                    'employee_id' => $emp->id,
                    'date' => $date->format('Y-m-d'),

                    // CHECK IN
                    'check_in' => $checkIn->format('H:i:s'),
                    'check_in_lat' => -6.2000000 + rand(1, 100) / 10000,
                    'check_in_long' => 106.816666 + rand(1, 100) / 10000,
                    'check_in_photo' => 'attendance/sample-in.jpg',

                    // CHECK OUT
                    'check_out' => $checkOut->format('H:i:s'),
                    'check_out_lat' => -6.2000000 + rand(1, 100) / 10000,
                    'check_out_long' => 106.816666 + rand(1, 100) / 10000,
                    'check_out_photo' => 'attendance/sample-out.jpg',

                    // RESULT
                    'working_minutes' => $workingMinutes,
                    'task' => $this->randomTask(),

                    // VALIDATION
                    'is_within_radius' => true,
                    'is_face_valid' => true,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function randomTask()
    {
        $tasks = [
            'Melakukan pengecekan stok barang di gudang',
            'Input data barang masuk ke sistem',
            'Melakukan packing dan pengiriman',
            'Koordinasi dengan tim operasional',
            'Maintenance alat produksi',
            'Monitoring aktivitas gudang',
            'Membuat laporan harian',
            'Quality control barang masuk',
        ];

        return $tasks[array_rand($tasks)];
    }
}