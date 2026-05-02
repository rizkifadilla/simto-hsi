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
        $employees = Employee::with('client')->get();

        foreach ($employees as $emp) {

            // =========================
            // 1. BULAN LALU (FULL)
            // =========================
            $startLastMonth = Carbon::now()->subMonth()->startOfMonth();
            $endLastMonth   = Carbon::now()->subMonth()->endOfMonth();

            for ($date = $startLastMonth->copy(); $date <= $endLastMonth; $date->addDay()) {

                // skip weekend
                if (!$date->isWeekday()) continue;

                // skip random (biar ada bolos)
                if (rand(1, 10) <= 2) continue;

                $this->createAttendance($emp, $date);
            }

            // =========================
            // 2. BULAN SEKARANG (SAMPAI HARI INI)
            // =========================
            $startThisMonth = Carbon::now()->startOfMonth();
            $today = Carbon::now();

            for ($date = $startThisMonth->copy(); $date <= $today; $date->addDay()) {

                if (!$date->isWeekday()) continue;

                if (rand(1, 10) <= 2) continue;

                $this->createAttendance($emp, $date);
            }
        }
    }

    private function createAttendance($emp, $date)
    {
        $client = $emp->client;

        // default kalau client belum ada
        $checkInLimit = $client->check_in_time ?? '08:00:00';
        $checkOutLimit = $client->check_out_time ?? '17:00:00';

        // 90% tepat waktu
        $isOnTime = rand(1, 10) <= 9;

        if ($isOnTime) {
            // TEPAT WAKTU
            $checkIn = Carbon::parse($date->format('Y-m-d') . ' ' . $checkInLimit)
                ->subMinutes(rand(0, 30));

            $checkOut = Carbon::parse($date->format('Y-m-d') . ' ' . $checkOutLimit)
                ->addMinutes(rand(0, 30));
        } else {
            // TELAT / PULANG CEPAT
            $checkIn = Carbon::parse($date->format('Y-m-d') . ' ' . $checkInLimit)
                ->addMinutes(rand(1, 60));

            $checkOut = Carbon::parse($date->format('Y-m-d') . ' ' . $checkOutLimit)
                ->subMinutes(rand(1, 60));
        }

        $workingMinutes = $checkOut->diffInMinutes($checkIn);

        Attendance::create([
            'employee_id' => $emp->id,
            'date' => $date->format('Y-m-d'),

            // CHECK IN
            'check_in' => $checkIn->format('H:i:s'),
            'check_in_lat' => $client->latitude ?? -6.200000,
            'check_in_long' => $client->longitude ?? 106.816666,
            'check_in_photo' => 'attendance/sample-in.jpg',

            // CHECK OUT
            'check_out' => $checkOut->format('H:i:s'),
            'check_out_lat' => $client->latitude ?? -6.200000,
            'check_out_long' => $client->longitude ?? 106.816666,
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