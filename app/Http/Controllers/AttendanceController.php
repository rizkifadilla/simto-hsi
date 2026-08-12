<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use stdClass;

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
            'type_menu' => 'attendance',
            'useDistance'=> $employee->absent_using_distance ?? false,
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
        $isFaceValid   = true;

        if (!$employee->face_descriptor) {

            // FIRST REGISTER FACE
            $employee->update([
                'face_descriptor' => json_encode($newDescriptor)
            ]);

        } else {

            $savedDescriptor = json_decode($employee->face_descriptor, true);

            $faceDistance = $this->faceDistance($savedDescriptor, $newDescriptor);

            if ($faceDistance > 0.45) {
                return back()->with('error', '❌ Face not match!');
            }
        }

        // ======================
        // LOCATION VALIDATION
        // ======================
        $client = $employee->client;
        $isWithinRadius = false;

        if ($employee->absent_using_distance && $client) {

            $locationDistance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $client->latitude,
                $client->longitude
            );

            if ($locationDistance <= $client->attendance_radius) {
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
        $today = now()->format('Y-m-d');

        $attendance = Attendance::firstOrCreate([
            'employee_id' => $employee->id,
            'date'        => $today
        ]);

        // ======================
        // CHECK IN
        // ======================
        if (!$attendance->check_in) {

            $attendance->update([
                'check_in'            => now()->format('H:i:s'),
                'check_in_lat'        => $request->latitude,
                'check_in_long'       => $request->longitude,
                'check_in_photo'      => $fileName,
                'is_within_radius'    => $isWithinRadius,
                'is_face_valid'       => $isFaceValid,
                'ip_address_check_in' => $request->getClientIp(),
            ]);

        } 
        // ======================
        // CHECK OUT
        // ======================
        else {

            $checkInTime    = Carbon::createFromFormat('H:i:s', $attendance->check_in);
            $now            = now();
            $workingMinutes = $now->diffInMinutes($checkInTime);

            $attendance->update([
                'check_out'            => $now->format('H:i:s'),
                'check_out_lat'        => $request->latitude,
                'check_out_long'       => $request->longitude,
                'check_out_photo'      => $fileName,
                'working_minutes'      => $workingMinutes,
                'task'                 => $request->task,
                'ip_address_check_out' => $request->getClientIp(),
            ]);
        }

        return back()->with('success', '✅ Attendance success');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // hasil meter
    }

    public function myAttendance(Request $request)
    {
        $employee = auth()->user()->employee;

        // =========================================================
        // FILTER
        // =========================================================

        $month    = $request->month;
        $from     = $request->from;
        $to       = $request->to;
        $status   = $request->status;
        $location = $request->location;
        $face     = $request->face;


        // =========================================================
        // DEFAULT MONTH
        // Kalau tidak memilih month, gunakan bulan sekarang
        // =========================================================

        if (!$month && !$from && !$to) {
            $month = now()->format('Y-m');
        }


        // =========================================================
        // TENTUKAN RANGE TANGGAL
        // =========================================================

        if ($month) {

            try {
                $monthDate = Carbon::createFromFormat(
                    'Y-m',
                    $month
                )->startOfMonth();

                $periodStart = $monthDate->copy()->startOfMonth();
                $periodEnd   = $monthDate->copy()->endOfMonth();

            } catch (\Exception $e) {

                $month = now()->format('Y-m');

                $monthDate = Carbon::createFromFormat(
                    'Y-m',
                    $month
                )->startOfMonth();

                $periodStart = $monthDate->copy()->startOfMonth();
                $periodEnd   = $monthDate->copy()->endOfMonth();
            }

        } else {

            $periodStart = $from
                ? Carbon::parse($from)->startOfDay()
                : now()->startOfMonth();

            $periodEnd = $to
                ? Carbon::parse($to)->endOfDay()
                : now()->endOfDay();
        }


        // =========================================================
        // JANGAN TAMPILKAN TANGGAL MASA DEPAN
        //
        // Kalau bulan sekarang:
        // contoh hari ini 9 Agustus
        // tanggal 10-31 tidak dibuat sebagai Absent
        //
        // Kalau bulan sebelumnya:
        // semua tanggal sampai akhir bulan bisa ditampilkan.
        // =========================================================

        $today = now()->startOfDay();

        if ($periodEnd->greaterThan($today)) {
            $periodEnd = $today->copy();
        }


        // Kalau range akhirnya lebih kecil dari awal
        if ($periodEnd->lt($periodStart)) {

            $attendances = collect();

            return view('pages.attendance.history', [

                'type_menu' => 'myattendance',

                'attendances' => $attendances,

                'month' => $month,
                'from' => $from,
                'to' => $to,
                'status' => $status,
                'location' => $location,
                'face' => $face,

                'totalDays' => 0,
                'totalPresent' => 0,
                'totalPermit' => 0,
                'totalLeave' => 0,
                'totalSick' => 0,
                'totalAbsent' => 0,

            ]);
        }


        // =========================================================
        // AMBIL DATA ATTENDANCE
        // =========================================================

        $attendanceQuery = Attendance::where(
            'employee_id',
            $employee->id
        )
            ->whereBetween('date', [
                $periodStart->format('Y-m-d'),
                $periodEnd->format('Y-m-d')
            ]);


        $attendanceData = $attendanceQuery
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->date)
                    ->format('Y-m-d');
            });


        // =========================================================
        // BUAT DATA HARI KERJA
        //
        // Sabtu & Minggu TIDAK ditampilkan di web.
        //
        // Hari kerja yang belum terjadi juga tidak dibuat.
        // =========================================================

        $attendances = collect();

        $period = CarbonPeriod::create(
            $periodStart,
            $periodEnd
        );


        foreach ($period as $date) {

            // =====================================================
            // SKIP SABTU & MINGGU
            // =====================================================

            if (in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                continue;
            }


            $key = $date->format('Y-m-d');

            $attendance = $attendanceData->get($key);


            // =====================================================
            // ADA DATA ATTENDANCE
            // =====================================================

            if ($attendance) {

                $attendance->is_absent = false;

                $attendances->push($attendance);

                continue;
            }


            // =====================================================
            // TIDAK ADA DATA
            //
            // Karena periodEnd sudah dibatasi sampai hari ini,
            // data kosong di sini berarti hari kerja yang sudah lewat.
            // Jadi dianggap ABSENT.
            // =====================================================

            $absent = new stdClass();

            $absent->id = null;

            $absent->employee_id = $employee->id;

            $absent->date = $key;

            $absent->check_in = null;
            $absent->check_out = null;

            $absent->check_in_lat = null;
            $absent->check_in_long = null;

            $absent->check_out_lat = null;
            $absent->check_out_long = null;

            $absent->working_minutes = null;

            $absent->is_within_radius = null;
            $absent->is_face_valid = null;

            $absent->task = null;

            $absent->is_absent = true;

            $attendances->push($absent);
        }


        // =========================================================
        // FILTER STATUS
        // =========================================================

        $attendances = $attendances->filter(function ($attendance) use ($status) {

            if (!$status) {
                return true;
            }


            // -----------------------------------------------------
            // ABSENT
            // -----------------------------------------------------

            if ($status === 'absent') {

                return $attendance->is_absent === true;
            }


            // -----------------------------------------------------
            // PRESENT
            // -----------------------------------------------------

            if ($status === 'hadir') {

                return !$attendance->is_absent
                    && !empty($attendance->check_in)
                    && !in_array(
                        strtolower($attendance->task ?? ''),
                        ['izin', 'cuti', 'sakit']
                    );
            }


            // -----------------------------------------------------
            // PERMIT
            // -----------------------------------------------------

            if ($status === 'izin') {

                return !$attendance->is_absent
                    && strtolower($attendance->task ?? '') === 'izin';
            }


            // -----------------------------------------------------
            // LEAVE
            // -----------------------------------------------------

            if ($status === 'cuti') {

                return !$attendance->is_absent
                    && strtolower($attendance->task ?? '') === 'cuti';
            }


            // -----------------------------------------------------
            // SICK
            // -----------------------------------------------------

            if ($status === 'sakit') {

                return !$attendance->is_absent
                    && strtolower($attendance->task ?? '') === 'sakit';
            }


            return true;
        });


        // =========================================================
        // FILTER LOCATION
        // =========================================================

        if ($location === 'valid') {

            $attendances = $attendances->filter(function ($attendance) {

                return !$attendance->is_absent
                    && $attendance->is_within_radius === true;
            });

        } elseif ($location === 'outside') {

            $attendances = $attendances->filter(function ($attendance) {

                return !$attendance->is_absent
                    && $attendance->is_within_radius === false;
            });
        }


        // =========================================================
        // FILTER FACE
        // =========================================================

        if ($face === 'valid') {

            $attendances = $attendances->filter(function ($attendance) {

                return !$attendance->is_absent
                    && $attendance->is_face_valid === true;
            });

        } elseif ($face === 'invalid') {

            $attendances = $attendances->filter(function ($attendance) {

                return !$attendance->is_absent
                    && $attendance->is_face_valid === false;
            });
        }


        // =========================================================
        // SORT TANGGAL ASCENDING
        //
        // 01
        // 02
        // 03
        // ...
        // 30
        // 31
        // =========================================================

        $attendances = $attendances
            ->sortBy(function ($attendance) {

                return Carbon::parse($attendance->date)
                    ->format('Y-m-d');
            })
            ->values();


        // =========================================================
        // SUMMARY
        // MENGIKUTI HASIL FILTER
        // =========================================================

        $totalDays = $attendances->count();


        // PRESENT

        $totalPresent = $attendances
            ->filter(function ($attendance) {

                return !$attendance->is_absent
                    && !empty($attendance->check_in)
                    && !in_array(
                        strtolower($attendance->task ?? ''),
                        ['izin', 'cuti', 'sakit']
                    );
            })
            ->count();


        // PERMIT

        $totalPermit = $attendances
            ->filter(function ($attendance) {

                return !$attendance->is_absent
                    && strtolower($attendance->task ?? '') === 'izin';
            })
            ->count();


        // LEAVE

        $totalLeave = $attendances
            ->filter(function ($attendance) {

                return !$attendance->is_absent
                    && strtolower($attendance->task ?? '') === 'cuti';
            })
            ->count();


        // SICK

        $totalSick = $attendances
            ->filter(function ($attendance) {

                return !$attendance->is_absent
                    && strtolower($attendance->task ?? '') === 'sakit';
            })
            ->count();


        // ABSENT

        $totalAbsent = $attendances
            ->filter(function ($attendance) {

                return $attendance->is_absent === true;
            })
            ->count();


        // =========================================================
        // RETURN VIEW
        // =========================================================

        return view('pages.attendance.history', [

            'type_menu' => 'myattendance',

            'attendances' => $attendances,

            // filter
            'month' => $month,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'location' => $location,
            'face' => $face,

            // summary
            'totalDays' => $totalDays,
            'totalPresent' => $totalPresent,
            'totalPermit' => $totalPermit,
            'totalLeave' => $totalLeave,
            'totalSick' => $totalSick,
            'totalAbsent' => $totalAbsent,

        ]);
    }


    // =============================================================
    // EXPORT EXCEL
    // =============================================================

    public function export(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $employee = auth()->user()
            ->employee
            ->load('client');

        $attendance = Attendance::where(
            'employee_id',
            $employee->id
        )
            ->whereBetween('date', [
                $request->from,
                $request->to
            ])
            ->orderBy('date')
            ->get()
            ->keyBy(function ($a) {

                return Carbon::parse($a->date)
                    ->format('Y-m-d');

            });

        $periode =
            Carbon::parse($request->from)
                ->translatedFormat('j F Y')
            . ' – '
            . Carbon::parse($request->to)
                ->translatedFormat('j F Y');


        // summary export
        $summary = $this->calculateExportSummary(
            $attendance,
            $request->from,
            $request->to
        );


        $html = $this->buildExcelHtml(
            $employee,
            $attendance,
            $request->from,
            $request->to,
            $periode,
            $summary
        );


        $filename =
            'timesheet_'
            . now()->format('YmdHis')
            . '.xls';


        return response($html, 200, [

            'Content-Type' =>
                'application/vnd.ms-excel',

            'Content-Disposition' =>
                "attachment; filename=\"{$filename}\"",

            'Cache-Control' =>
                'max-age=0',
        ]);
    }


    // =============================================================
    // EXPORT PDF
    // =============================================================

    public function exportPdf(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $employee = auth()->user()->employee->load('client');

        // =========================================================
        // ATTENDANCE
        // =========================================================

        $attendance = Attendance::where(
            'employee_id',
            $employee->id
        )
            ->whereBetween('date', [
                $request->from,
                $request->to
            ])
            ->orderBy('date')
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->date)
                    ->format('Y-m-d');
            });


        // =========================================================
        // PERIOD
        // =========================================================

        $period = CarbonPeriod::create(
            $request->from,
            $request->to
        );


        // =========================================================
        // SUMMARY
        // =========================================================

        $totalDays = 0;
        $totalPresent = 0;
        $totalPermit = 0;
        $totalLeave = 0;
        $totalSick = 0;
        $totalAbsent = 0;


        foreach ($period as $date) {

            $key = $date->format('Y-m-d');

            $att = $attendance->get($key);


            // ---------------------------------------------------------
            // WEEKEND
            // ---------------------------------------------------------

            if (in_array($date->dayOfWeek, [0, 6])) {
                continue;
            }


            // ---------------------------------------------------------
            // HARI KERJA
            // ---------------------------------------------------------

            $totalDays++;


            // Tidak ada attendance
            // berarti ABSENT
            if (!$att) {

                $totalAbsent++;

                continue;
            }


            $task = strtolower(
                trim($att->task ?? '')
            );


            // ---------------------------------------------------------
            // PERMIT
            // ---------------------------------------------------------

            if ($task === 'izin') {

                $totalPermit++;

                continue;
            }


            // ---------------------------------------------------------
            // LEAVE
            // ---------------------------------------------------------

            if ($task === 'cuti') {

                $totalLeave++;

                continue;
            }


            // ---------------------------------------------------------
            // SICK
            // ---------------------------------------------------------

            if ($task === 'sakit') {

                $totalSick++;

                continue;
            }


            // ---------------------------------------------------------
            // PRESENT
            // ---------------------------------------------------------

            if ($att->check_in) {

                $totalPresent++;

                continue;
            }


            // ---------------------------------------------------------
            // ABSENT
            // ---------------------------------------------------------

            $totalAbsent++;
        }


        // =========================================================
        // PERIODE
        // =========================================================

        $periode = Carbon::parse($request->from)
            ->translatedFormat('j F Y')
            . ' – '
            . Carbon::parse($request->to)
                ->translatedFormat('j F Y');


        // =========================================================
        // VIEW PDF
        // =========================================================

        return view('pages.attendance.pdf', [

            'employee' => $employee,

            'attendance' => $attendance,

            'period' => CarbonPeriod::create(
                $request->from,
                $request->to
            ),

            'periode' => $periode,

            // Summary
            'totalDays' => $totalDays,

            'totalPresent' => $totalPresent,

            'totalPermit' => $totalPermit,

            'totalLeave' => $totalLeave,

            'totalSick' => $totalSick,

            'totalAbsent' => $totalAbsent,
        ]);
    }


    // =============================================================
    // SUMMARY EXPORT
    // =============================================================

    private function calculateExportSummary(
        $attendance,
        string $from,
        string $to
    ) {

        $totalDays = 0;
        $totalPresent = 0;
        $totalPermit = 0;
        $totalLeave = 0;
        $totalSick = 0;
        $totalAbsent = 0;


        $period = CarbonPeriod::create(
            Carbon::parse($from),
            Carbon::parse($to)
        );


        foreach ($period as $date) {

            // Weekend tidak dihitung sebagai attendance
            if ($date->isWeekend()) {
                continue;
            }


            $totalDays++;


            $key = $date->format('Y-m-d');

            $att = $attendance->get($key);


            // Tidak ada attendance record
            if (!$att) {

                $totalAbsent++;

                continue;
            }


            // Izin
            if ($att->task === 'izin') {

                $totalPermit++;

                continue;
            }


            // Cuti
            if ($att->task === 'cuti') {

                $totalLeave++;

                continue;
            }


            // Sakit
            if ($att->task === 'sakit') {

                $totalSick++;

                continue;
            }


            // Hadir
            if ($att->check_in) {

                $totalPresent++;

                continue;
            }


            // Tidak hadir
            $totalAbsent++;
        }


        return [

            'totalDays' => $totalDays,

            'totalPresent' => $totalPresent,

            'totalPermit' => $totalPermit,

            'totalLeave' => $totalLeave,

            'totalSick' => $totalSick,

            'totalAbsent' => $totalAbsent,

        ];
    }


    // =============================================================
    // BUILD EXCEL
    // =============================================================

    private function buildExcelHtml(
        $employee,
        $attendance,
        string $from,
        string $to,
        string $periode,
        array $summary
    ): string {

        $client = $employee->client;


        // Jam kerja client
        $timeIn =
            $client?->check_in_time
                ? Carbon::parse(
                    $client->check_in_time
                )->format('H:i')
                : '';


        $timeOut =
            $client?->check_out_time
                ? Carbon::parse(
                    $client->check_out_time
                )->format('H:i')
                : '';


        $workingHours = '';


        if (
            $client?->check_in_time
            && $client?->check_out_time
        ) {

            $mins =
                Carbon::parse(
                    $client->check_out_time
                )->diffInMinutes(
                    Carbon::parse(
                        $client->check_in_time
                    )
                );


            $workingHours =
                sprintf(
                    '%d:%02d',
                    intdiv($mins, 60),
                    $mins % 60
                );
        }


        $rows = '';

        $no = 1;


        $period = CarbonPeriod::create(
            $from,
            $to
        );


        foreach ($period as $date) {

            $key = $date->format('Y-m-d');

            $att = $attendance->get($key);

            $isWeekend = $date->isWeekend();

            $hasWork =
                $att
                && $att->check_in;


            // =====================================================
            // TASK
            // =====================================================

            $task =
                $att
                    ? nl2br(
                        e($att->task ?? '')
                    )
                    : '';


            // =====================================================
            // STATUS ABSENT
            // =====================================================

            if (
                !$isWeekend
                && !$att
            ) {

                $task = 'Absent';

            } elseif (
                !$isWeekend
                && $att
                && !$hasWork
                && !$att->task
            ) {

                $task = 'Absent';
            }


            // =====================================================
            // WEEKEND
            // =====================================================

            if ($isWeekend) {

                $rowBg = '#D9D9D9';

                $bold = 'font-weight:bold;';

                if (!$task) {

                    $task =
                        $date->dayOfWeek === 6
                            ? 'Sabtu'
                            : 'Minggu';
                }

            } elseif (
                $att
                && !$hasWork
                && $att->task
            ) {

                // Izin / Cuti / Sakit
                $rowBg = '#FCE5CD';

                $bold = 'font-weight:bold;';

            } elseif (
                !$att
                || !$hasWork
            ) {

                // Absent
                $rowBg = '#FCE8E8';

                $bold = 'font-weight:bold;';

            } else {

                $rowBg = '#FFFFFF';

                $bold = '';
            }


            // =====================================================
            // TIME
            // =====================================================

            $rowTimeIn =
                $hasWork
                    ? $timeIn
                    : '';


            $rowTimeOut =
                $hasWork
                    ? $timeOut
                    : '';


            $rowWorkHours =
                $hasWork
                    ? $workingHours
                    : '';


            $rows .= "
                <tr style=\"background:{$rowBg};\">

                    <td style=\"text-align:center;{$bold}\">
                        {$no}
                    </td>

                    <td style=\"text-align:center;{$bold}\">
                        {$date->format('j F Y')}
                    </td>

                    <td style=\"text-align:center;\">
                        {$rowTimeIn}
                    </td>

                    <td style=\"text-align:center;\">
                        {$rowTimeOut}
                    </td>

                    <td style=\"text-align:center;\">
                        {$rowWorkHours}
                    </td>

                    <td style=\"text-align:left;white-space:pre-wrap;\">
                        {$task}
                    </td>

                </tr>
            ";


            $no++;
        }


        $companyName =
            config(
                'app.company_name',
                'PT. Hermes Solusi Integrasi'
            );


        $companyAddress =
            config(
                'app.company_address',
                '88@Kasablanka Office Tower, Lantai 3, Unit A Jl. Kasablanka Kav. 88, DKI Jakarta, 12870'
            );


        $employeeName =
            e(
                $employee->full_name ?? '-'
            );


        $employeeRole =
            e(
                $employee->position ?? '-'
            );


        $clientName =
            e(
                $client->name ?? '-'
            );


        $clientAddress =
            e(
                $client->address ?? '-'
            );


        return <<<HTML
<html>
<head>

<meta charset="UTF-8"/>

<style>

    body,
    table,
    td,
    th {

        font-family: Arial, sans-serif;

        font-size: 10pt;
    }


    table {

        border-collapse: collapse;

        width: 100%;
    }


    td,
    th {

        border: 1px solid #BFBFBF;

        padding: 4px 8px;

        vertical-align: middle;
    }


    .nb td {

        border: none;
    }


    .company-name {

        font-size: 13pt;

        font-weight: bold;

        color: #1F4E79;

        border: none;
    }


    .company-address {

        font-size: 9pt;

        color: #555555;

        border: none;
    }


    .title-row td {

        background: #1F4E79;

        color: #FFFFFF;

        font-size: 14pt;

        font-weight: bold;

        text-align: center;

        border: none;

        padding: 8px;
    }


    .info-label {

        font-weight: bold;

        width: 130px;

        border: none;
    }


    .info-value {

        border: none;
    }


    .summary-header {

        background: #D9E2F3;

        font-weight: bold;

        text-align: center;
    }


    .col-header th {

        background: #1F4E79;

        color: #FFFFFF;

        text-align: center;

        font-weight: bold;

        padding: 6px 8px;
    }


    .weekend {

        background: #D9D9D9;
    }

</style>

</head>

<body>

<table>

    <tr class="nb">

        <td colspan="6"
            class="company-name">

            {$companyName}

        </td>

    </tr>


    <tr class="nb">

        <td colspan="6"
            class="company-address">

            {$companyAddress}

        </td>

    </tr>


    <tr class="nb">

        <td colspan="6"
            class="company-address">

            Client: {$clientName}

        </td>

    </tr>


    <tr class="nb">

        <td colspan="6"
            class="company-address">

            Client Address: {$clientAddress}

        </td>

    </tr>


    <tr>
        <td colspan="6"
            style="border:none;height:10px;">
        </td>
    </tr>


    <tr class="title-row">

        <td colspan="6">
            TIMESHEET
        </td>

    </tr>


    <tr>
        <td colspan="6"
            style="border:none;height:10px;">
        </td>
    </tr>


    <tr class="nb">

        <td class="info-label">
            Consultant Name
        </td>

        <td colspan="5"
            class="info-value">

            : {$employeeName}

        </td>

    </tr>


    <tr class="nb">

        <td class="info-label">
            Role
        </td>

        <td colspan="5"
            class="info-value">

            : {$employeeRole}

        </td>

    </tr>


    <tr class="nb">

        <td class="info-label">
            Client
        </td>

        <td colspan="5"
            class="info-value">

            : {$clientName}

        </td>

    </tr>


    <tr class="nb">

        <td class="info-label">
            Periode
        </td>

        <td colspan="5"
            class="info-value">

            : {$periode}

        </td>

    </tr>


    <tr>
        <td colspan="6"
            style="border:none;height:10px;">
        </td>
    </tr>


    <!-- SUMMARY -->

    <tr>

        <td colspan="6"
            class="summary-header">

            ATTENDANCE SUMMARY

        </td>

    </tr>


    <tr>

        <td>
            Working Days
        </td>

        <td style="text-align:center;">
            {$summary['totalDays']}
        </td>

        <td>
            Present
        </td>

        <td style="text-align:center;">
            {$summary['totalPresent']}
        </td>

        <td>
            Absent
        </td>

        <td style="text-align:center;">
            {$summary['totalAbsent']}
        </td>

    </tr>


    <tr>

        <td>
            Permit
        </td>

        <td style="text-align:center;">
            {$summary['totalPermit']}
        </td>

        <td>
            Leave
        </td>

        <td style="text-align:center;">
            {$summary['totalLeave']}
        </td>

        <td>
            Sick
        </td>

        <td style="text-align:center;">
            {$summary['totalSick']}
        </td>

    </tr>


    <tr>
        <td colspan="6"
            style="border:none;height:10px;">
        </td>
    </tr>


    <!-- TABLE HEADER -->

    <tr class="col-header">

        <th style="width:35px;">
            No.
        </th>

        <th style="width:120px;">
            Date
        </th>

        <th style="width:75px;">
            Time In
        </th>

        <th style="width:75px;">
            Time Out
        </th>

        <th style="width:110px;">
            Working Hours
        </th>

        <th>
            Task
        </th>

    </tr>


    {$rows}

</table>

</body>
</html>
HTML;
    }

    public function manual(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:sakit,izin,cuti',
        ]);

        Attendance::create([
            'employee_id' => auth()->user()->employee->id,
            'date' => $request->date,

            // kosong karena bukan hadir
            'check_in' => null,
            'check_out' => null,

            'working_minutes' => 0,

            // MASUK KE TASK
            'task' => $request->type,

            'is_within_radius' => false,
            'is_face_valid' => false,
        ]);

        return back()->with('success', 'Manual absent saved successfully');
    }

    public function monitoring(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to ?? now()->endOfMonth()->format('Y-m-d');

        $clientId = $request->client_id;
        $division = $request->division;

        // =========================
        // DATE RANGE (ONLY WEEKDAY)
        // =========================
        $dates = collect(
            CarbonPeriod::create($from, $to)
        )->filter(function ($date) {
            return !in_array($date->dayOfWeek, [0, 6]); // skip sunday & saturday
        });

        // =========================
        // EMPLOYEE QUERY
        // =========================
        $employees = Employee::with([
                'client',
                'attendances' => function ($q) use ($from, $to) {
                    $q->whereBetween('date', [$from, $to]);
                }
            ])

            ->when($clientId, function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })

            ->when($division, function ($q) use ($division) {
                $q->where('division', $division);
            })

            ->orderBy('full_name')
            ->get();

        // =========================
        // FILTER DATA
        // =========================
        $clients = Client::orderBy('name')->get();

        $divisions = Employee::select('division')
            ->whereNotNull('division')
            ->distinct()
            ->pluck('division');

        return view('pages.attendance.monitoring', [
            'employees' => $employees,
            'dates' => $dates,
            'from' => $from,
            'to' => $to,
            'clients' => $clients,
            'divisions' => $divisions,
            'clientId' => $clientId,
            'division' => $division,
            'type_menu' => 'attendance-monitoring',
        ]);
    }

    public function exportMonitoring(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to ?? now()->endOfMonth()->format('Y-m-d');

        $clientId = $request->client_id;
        $division = $request->division;

        $dates = collect(
            CarbonPeriod::create($from, $to)
        )->filter(function ($date) {
            return !in_array($date->dayOfWeek, [0, 6]);
        });

        $employees = Employee::with([
                'client',
                'attendances' => function ($q) use ($from, $to) {
                    $q->whereBetween('date', [$from, $to]);
                }
            ])
            ->when($clientId, function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })
            ->when($division, function ($q) use ($division) {
                $q->where('division', $division);
            })
            ->orderBy('full_name')
            ->get();

        $html = $this->buildMonitoringHtml(
            $employees,
            $dates,
            $from,
            $to
        );

        $filename = 'attendance_monitoring_' .
            now()->format('YmdHis') . '.xls';

        return response($html)
            ->header(
                'Content-Type',
                'application/vnd.ms-excel; charset=UTF-8'
            )
            ->header(
                'Content-Disposition',
                "attachment; filename=\"{$filename}\""
            );
    }

    private function buildMonitoringHtml(
        $employees,
        $dates,
        string $from,
        string $to
    ): string {

        $headers = '';

        foreach ($dates as $date) {
            $headers .= '
                <th style="min-width:80px;">
                    ' . $date->format('d') . '<br>
                    ' . $date->translatedFormat('D') . '
                </th>';
        }

        $rows = '';

        foreach ($employees as $employee) {

            $row = '
                <tr>
                    <td style="white-space: nowrap;">
                        <strong>' . e($employee->full_name) . '</strong><br>
                        <small>' . e($employee->division) . '</small>
                    </td>';

            foreach ($dates as $date) {

                $attendance = $employee->attendances
                    ->where('date', $date->format('Y-m-d'))
                    ->first();

                $bg = '#FFFFFF';
                $value = '-';

                if (!$attendance) {
                    $bg = '#F4CCCC';
                    $value = '❌';
                } elseif ($attendance->task == 'izin') {
                    $bg = '#D9EAD3';
                    $value = 'Permit';
                } elseif ($attendance->task == 'cuti') {
                    $bg = '#D9EAD3';
                    $value = 'Leave';
                } elseif ($attendance->task == 'sakit') {
                    $bg = '#FFF2CC';
                    $value = 'Sick';
                } elseif ($attendance->check_in) {
                    $bg = '#B6D7A8';
                    $value = '✅';
                }

                $row .= '
                    <td style="
                        background:' . $bg . ';
                        text-align:center;
                        vertical-align:middle;
                    ">
                        ' . $value . '
                    </td>';
            }

            $row .= '</tr>';

            $rows .= $row;
        }

        $periode = Carbon::parse($from)->format('d M Y')
            . ' - ' .
            Carbon::parse($to)->format('d M Y');

        $totalCol = $dates->count() + 1;

        return <<<HTML
            <html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns:v="urn:schemas-microsoft-com:vml"
                xmlns="http://www.w3.org/TR/REC-html40">

            <head>
            <meta charset="UTF-8"/>

            <style>
            body, table, td, th {
                font-family: Arial, sans-serif;
                font-size: 10pt;
            }

            table {
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid #BFBFBF;
                padding: 5px;
            }

            .title {
                background: #1F4E79;
                color: white;
                font-size: 14pt;
                font-weight: bold;
                text-align: center;
            }

            .header {
                background: #1F4E79;
                color: white;
                font-weight: bold;
                text-align: center;
            }
            </style>
            </head>

            <body>

            <table>

            <tr>
                <td colspan="{$totalCol}" class="title">
                    Attendance Monitoring
                </td>
            </tr>

            <tr>
                <td colspan="{$totalCol}">
                    Period: {$periode}
                </td>
            </tr>

            <tr>
                <td colspan="{$totalCol}">&nbsp;</td>
            </tr>

            <tr class="header">
                <th style="min-width:220px;">Employee</th>
                {$headers}
            </tr>

            {$rows}

            </table>

            </body>
            </html>
            HTML;
    }
}
