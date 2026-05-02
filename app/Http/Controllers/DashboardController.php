<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ================= BASIC =================
        $totalEmployee = Employee::count();

        $presentToday = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->count();

        $notPresent = $totalEmployee - $presentToday;

        // ================= AVG WORK HOUR =================
        $avgWorkMinutes = Attendance::whereNotNull('working_minutes')
            ->avg('working_minutes');

        $avgWorkHour = $avgWorkMinutes
            ? round($avgWorkMinutes / 60, 1)
            : 0;

        // ================= LINE CHART (7 HARI) =================
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $labels[] = $date->format('d M');

            $count = Attendance::whereDate('date', $date)
                ->whereNotNull('check_in')
                ->count();

            $data[] = $count;
        }

        // ================= PIE =================
        $attendanceSummary = [
            'hadir' => $presentToday,
            'tidak' => $notPresent
        ];

        // ================= AVG CHECK IN =================
        $avgCheckInRaw = Attendance::select(
                DB::raw('DATE(date) as tanggal'),
                DB::raw('AVG(TIME_TO_SEC(check_in)) as avg_seconds')
            )
            ->whereNotNull('check_in')
            ->where('date', '>=', now()->subDays(6))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $avgCheckInLabels = [];
        $avgCheckInData = [];

        foreach ($avgCheckInRaw as $row) {
            $avgCheckInLabels[] = Carbon::parse($row->tanggal)->format('d M');

            // 🔥 tetap menit (nanti diformat di chart jadi jam)
            $avgCheckInData[] = round($row->avg_seconds / 60);
        }

        // ================= RANKING =================
        $start = Carbon::now()->subMonth()->startOfMonth();
        $end   = Carbon::now()->subMonth()->endOfMonth();

        // hitung hari kerja
        $workDays = 0;
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            if ($date->isWeekday()) $workDays++;
        }

        $ranking = Employee::select(
                'employees.id',
                'employees.full_name',

                DB::raw('COUNT(attendances.id) as total_hadir'),

                DB::raw("
                    SUM(
                        CASE 
                            WHEN TIME(attendances.check_in) <= clients.check_in_time
                            AND TIME(attendances.check_out) >= clients.check_out_time
                            THEN 1 ELSE 0 
                        END
                    ) as tepat_waktu
                "),

                // ✅ SCORE FIX MAX 10
                DB::raw("
                    ROUND(
                        (
                            (COUNT(attendances.id) / {$workDays}) * 7
                            +
                            (
                                SUM(
                                    CASE 
                                        WHEN TIME(attendances.check_in) <= clients.check_in_time
                                        AND TIME(attendances.check_out) >= clients.check_out_time
                                        THEN 1 ELSE 0 
                                    END
                                ) / NULLIF(COUNT(attendances.id),0) * 3
                            )
                        )
                    , 1)
                as score
                ")
            )

            ->leftJoin('attendances', function ($join) use ($start, $end) {
                $join->on('employees.id', '=', 'attendances.employee_id')
                    ->whereNotNull('attendances.check_in')
                    ->whereBetween('attendances.date', [$start, $end]);
            })

            ->leftJoin('clients', 'clients.id', '=', 'employees.client_id')

            ->groupBy('employees.id', 'employees.full_name')

            ->orderByDesc('score')
            ->orderByDesc('total_hadir')
            ->orderByDesc('tepat_waktu')

            ->limit(5)
            ->get();

        // ================= RECENT ACTIVITY =================
        $recentActivities = Attendance::with('employee')
            ->whereNotNull('task')
            ->latest()
            ->limit(5)
            ->get();

        return view('pages.dashboard-general-dashboard', [
            'type_menu' => 'dashboard',

            'totalEmployee' => $totalEmployee,
            'presentToday' => $presentToday,
            'notPresent' => $notPresent,
            'avgWorkHour' => $avgWorkHour,

            'labels' => $labels,
            'data' => $data,

            'attendanceSummary' => $attendanceSummary,

            'avgCheckInLabels' => $avgCheckInLabels,
            'avgCheckInData' => $avgCheckInData,

            'ranking' => $ranking,
            'recentActivities' => $recentActivities
        ]);
    }
}