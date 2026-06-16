<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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

    // public function export(Request $request)
    // {
    //     $request->validate([
    //         'from' => 'required|date',
    //         'to' => 'required|date|after_or_equal:from'
    //     ]);

    //     $employeeId = auth()->user()->employee->id;

    //     $data = Attendance::where('employee_id', $employeeId)
    //         ->whereBetween('date', [$request->from, $request->to])
    //         ->orderBy('date', 'desc')
    //         ->get();

    //     $filename = "attendance_" . now()->format('YmdHis') . ".csv";

    //     $headers = [
    //         "Content-type" => "text/csv",
    //         "Content-Disposition" => "attachment; filename=$filename",
    //     ];

    //     $callback = function () use ($data) {
    //         $file = fopen('php://output', 'w');

    //         // HEADER
    //         fputcsv($file, [
    //             'Tanggal',
    //             'Check In',
    //             'Check Out',
    //             'Durasi (Jam)',
    //             'Lokasi',
    //             'Face',
    //             'Kegiatan'
    //         ]);

    //         foreach ($data as $row) {
    //             fputcsv($file, [
    //                 \Carbon\Carbon::parse($row->date)->format('d-m-Y'),
    //                 $row->check_in,
    //                 $row->check_out,
    //                 $row->working_minutes ? round($row->working_minutes / 60, 2) : 0,
    //                 $row->is_within_radius ? 'Valid' : 'Diluar',
    //                 $row->is_face_valid ? 'Valid' : 'Invalid',
    //                 $row->task
    //             ]);
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    public function export(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $employee = auth()->user()->employee->load('client');

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$request->from, $request->to])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($a) => Carbon::parse($a->date)->format('Y-m-d'));

        $periode = Carbon::parse($request->from)->translatedFormat('j F Y')
            . ' – '
            . Carbon::parse($request->to)->translatedFormat('j F Y');

        $html = $this->buildHtml($employee, $attendance, $request->from, $request->to, $periode);

        $filename = 'timesheet_' . now()->format('YmdHis') . '.xls';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function buildHtml($employee, $attendance, string $from, string $to, string $periode): string
    {
        $client = $employee->client;

        // Jam & working hours selalu dari Client
        $timeIn  = $client?->check_in_time  ? Carbon::parse($client->check_in_time)->format('H:i')  : '';
        $timeOut = $client?->check_out_time ? Carbon::parse($client->check_out_time)->format('H:i') : '';

        $workingHours = '';
        if ($client?->check_in_time && $client?->check_out_time) {
            $mins         = Carbon::parse($client->check_out_time)->diffInMinutes(Carbon::parse($client->check_in_time));
            $workingHours = sprintf('%d:%02d', intdiv($mins, 60), $mins % 60);
        }

        $rows = '';
        $no   = 1;

        $period = CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            $key       = $date->format('Y-m-d');
            $att       = $attendance->get($key);
            $isWeekend = in_array($date->dayOfWeek, [0, 6]);
            $hasWork   = $att && $att->check_in;

            $task = $att ? nl2br(e($att->task ?? '')) : '';

            // Warna baris
            if ($isWeekend) {
                $rowBg = '#F2F2F2';
                $bold  = 'font-weight:bold;';
                $task  = $task ?: ($date->dayOfWeek === 6 ? 'Sabtu' : 'Minggu');
            } elseif ($att && !$hasWork && $att->task) {
                // Libur / Cuti
                $rowBg = '#FCE5CD';
                $bold  = 'font-weight:bold;';
            } else {
                $rowBg = '#FFFFFF';
                $bold  = '';
            }

            // Time In / Out / Working Hours hanya tampil kalau hari kerja & check_in ada
            $rowTimeIn    = $hasWork ? $timeIn    : '';
            $rowTimeOut   = $hasWork ? $timeOut   : '';
            $rowWorkHours = $hasWork ? $workingHours : '';

            $rows .= "
                <tr style=\"background:{$rowBg};\">
                    <td style=\"text-align:center;{$bold}\">{$no}</td>
                    <td style=\"text-align:center;{$bold}\">{$date->format('j F Y')}</td>
                    <td style=\"text-align:center;\">{$rowTimeIn}</td>
                    <td style=\"text-align:center;\">{$rowTimeOut}</td>
                    <td style=\"text-align:center;\">{$rowWorkHours}</td>
                    <td style=\"text-align:left;white-space:pre-wrap;\">{$task}</td>
                </tr>";

            $no++;
        }

        $companyName    = config('app.company_name', 'PT. Hermes Solusi Integrasi');
        $companyAddress = config('app.company_address', '88@Kasablanka Office Tower, Lantai 3, Unit A Jl. Kasablanka Kav. 88, DKI Jakarta, 12870');
        $employeeName   = e($employee->full_name ?? '-');
        $employeeRole   = e($employee->position  ?? '-');

        return <<<HTML
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:x="urn:schemas-microsoft-com:office:excel"
            xmlns:v="urn:schemas-microsoft-com:vml"
            xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="UTF-8"/>
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Timesheet</x:Name>
                            <x:WorksheetOptions>
                                <x:Print>
                                    <x:FitWidth>1</x:FitWidth>
                                    <x:FitHeight>0</x:FitHeight>
                                    <x:Landscape/>
                                </x:Print>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                body, table, td, th {
                    font-family: Arial, sans-serif;
                    font-size: 10pt;
                }
                table {
                    border-collapse: collapse;
                    width: 100%;
                }
                td, th {
                    border: 1px solid #BFBFBF;
                    padding: 4px 8px;
                    vertical-align: middle;
                }
                .nb td           { border: none; }
                .company-name    { font-size:13pt; font-weight:bold; color:#1F4E79; border:none; }
                .company-address { font-size:9pt; color:#555555; border:none; }
                .title-row td {
                    background:#1F4E79; color:#FFFFFF;
                    font-size:14pt; font-weight:bold;
                    text-align:center; border:none; padding:8px;
                }
                .spacer td  { border:none; padding:2px; }
                .info-label { font-weight:bold; width:130px; border:none; }
                .info-value { border:none; }
                .col-header th {
                    background:#1F4E79; color:#FFFFFF;
                    text-align:center; font-weight:bold; padding:6px 8px;
                }
            </style>
        </head>
        <body>
        <table>

            <!-- Logo + Perusahaan -->
            <tr class="nb">
                <td colspan="5" class="company-name">{$companyName}</td>
            </tr>
            <tr class="nb">
                <td colspan="5" class="company-address">{$companyAddress}</td>
            </tr>

            <tr class="spacer"><td colspan="6"></td></tr>

            <!-- Judul -->
            <tr class="title-row"><td colspan="6">TIMESHEET</td></tr>

            <tr class="spacer"><td colspan="6"></td></tr>

            <!-- Info karyawan -->
            <tr class="nb">
                <td class="info-label">Consultant Name</td>
                <td colspan="5" class="info-value">: {$employeeName}</td>
            </tr>
            <tr class="nb">
                <td class="info-label">Role</td>
                <td colspan="5" class="info-value">: {$employeeRole}</td>
            </tr>
            <tr class="nb">
                <td class="info-label">Periode</td>
                <td colspan="5" class="info-value">: {$periode}</td>
            </tr>

            <tr class="spacer"><td colspan="6"></td></tr>

            <!-- Header tabel -->
            <tr class="col-header">
                <th style="width:35px;">No.</th>
                <th style="width:120px;">Date</th>
                <th style="width:75px;">Time In</th>
                <th style="width:75px;">Time Out</th>
                <th style="width:110px;">Working Hours</th>
                <th>Task</th>
            </tr>

            <!-- Data -->
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
