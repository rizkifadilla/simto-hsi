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
        $ranking = Employee::select(
                'employees.id',
                'employees.full_name',
                DB::raw('COUNT(attendances.id) as total_hadir')
            )
            ->leftJoin('attendances', function ($join) {
                $join->on('employees.id', '=', 'attendances.employee_id')
                     ->whereNotNull('attendances.check_in');
            })
            ->groupBy('employees.id', 'employees.full_name')
            ->orderByDesc('total_hadir')
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