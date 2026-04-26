<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee()->with('client')->first();
        $today = date('Y-m-d');

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        return view('pages.attendance.index', [
            'attendance' => $attendance,
            'employee' => $employee,
            'clientLat' => $employee->client->latitude ?? null,
            'clientLng' => $employee->client->longitude ?? null,
            'radius' => $employee->client->attendance_radius ?? 100,
            'type_menu' => 'attendance'
        ]);
    }

    // HITUNG DISTANCE WAJAH
    private function faceDistance($desc1, $desc2)
    {
        $sum = 0;
        foreach ($desc1 as $i => $v) {
            $sum += pow($v - $desc2[$i], 2);
        }
        return sqrt($sum);
    }

    public function store(Request $request)
    {
        $employee = auth()->user()->employee;

        // ======================
        // VALIDATION
        // ======================
        $request->validate([
            'photo' => 'required',
            'face_descriptor' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // ======================
        // FACE VALIDATION
        // ======================
        $newDescriptor = json_decode($request->face_descriptor, true);

        $isFaceValid = true;

        if (!$employee->face_descriptor) {

            // first register
            $employee->update([
                'face_descriptor' => json_encode($newDescriptor)
            ]);

        } else {

            $savedDescriptor = json_decode($employee->face_descriptor, true);

            $distance = $this->faceDistance($savedDescriptor, $newDescriptor);

            if ($distance > 0.45) {
                return back()->with('error', '❌ Face not match! (' . $distance . ')');
            }
        }

        // ======================
        // LOCATION VALIDATION
        // ======================
        $client = $employee->client;

        $isWithinRadius = false;

        if ($employee->absent_using_distance && $client) {

            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $client->latitude,
                $client->longitude
            );

            if ($distance <= $client->attendance_radius) {
                $isWithinRadius = true;
            } else {
                return back()->with('error', '❌ Outside radius!');
            }

        } else {
            $isWithinRadius = true;
        }

        // ======================
        // SAVE PHOTO
        // ======================
        $image = $request->photo;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $fileName = 'attendance/' . time() . '.png';
        Storage::disk('public')->put($fileName, base64_decode($image));

        // ======================
        // ATTENDANCE
        // ======================
        $today = date('Y-m-d');

        $attendance = Attendance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => $today
        ]);

        // ======================
        // CHECK IN
        // ======================
        if (!$attendance->check_in) {

            $attendance->update([
                'check_in' => now()->format('H:i:s'),
                'check_in_lat' => $request->latitude,
                'check_in_long' => $request->longitude,
                'check_in_photo' => $fileName,
                'is_within_radius' => $isWithinRadius,
                'is_face_valid' => $isFaceValid,
            ]);

        } 
        // ======================
        // CHECK OUT
        // ======================
        else {

            // hitung durasi kerja
            $checkInTime = Carbon::createFromFormat('H:i:s', $attendance->check_in);
            $now = Carbon::now();

            $workingMinutes = $now->diffInMinutes($checkInTime);

            $attendance->update([
                'check_out' => now()->format('H:i:s'),
                'check_out_lat' => $request->latitude,
                'check_out_long' => $request->longitude,
                'check_out_photo' => $fileName,
                'working_minutes' => $workingMinutes,
                'task' => $request->task,
            ]);
        }

        return back()->with('success', '✅ Attendance success');
    }

    public function myAttendance()
    {
        $employee = auth()->user()->employee;

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->get();

        return view('pages.attendance.history', [
            'type_menu' => 'myattendance',
            'attendances' => $attendances
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ]);

        $employeeId = auth()->user()->employee->id;

        $data = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$request->from, $request->to])
            ->orderBy('date', 'desc')
            ->get();

        $filename = "attendance_" . now()->format('YmdHis') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // HEADER
            fputcsv($file, [
                'Tanggal',
                'Check In',
                'Check Out',
                'Durasi (Jam)',
                'Lokasi',
                'Face',
                'Kegiatan'
            ]);

            foreach ($data as $row) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($row->date)->format('d-m-Y'),
                    $row->check_in,
                    $row->check_out,
                    $row->working_minutes ? round($row->working_minutes / 60, 2) : 0,
                    $row->is_within_radius ? 'Valid' : 'Diluar',
                    $row->is_face_valid ? 'Valid' : 'Invalid',
                    $row->task
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}