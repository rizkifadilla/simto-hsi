<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\User;
use App\Models\Client;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // =========================================================
        // FILTER PARAMETER
        // =========================================================

        $clientId = $request->client_id;
        $division = $request->division;
        $status = $request->status;

        // Contract filter
        $contractExpiringFilter = $request->contract_expiring;


        // =========================================================
        // BASE QUERY
        // =========================================================

        $query = Employee::with('client');


        // =========================================================
        // FILTER CLIENT
        // =========================================================

        if ($clientId) {
            $query->where('client_id', $clientId);
        }


        // =========================================================
        // FILTER DIVISION
        // =========================================================

        if ($division) {
            $query->where('division', $division);
        }


        // =========================================================
        // FILTER EMPLOYEE STATUS
        // =========================================================

        if ($status) {
            $query->where('status', $status);
        }


        // =========================================================
        // FILTER CONTRACT
        // =========================================================

        if ($contractExpiringFilter) {

            $today = Carbon::today();

            // CONTRACT EXPIRING <= 30 DAYS
            if ($contractExpiringFilter == '30') {

                $thirtyDays = Carbon::today()->addDays(30);

                $query->whereNotNull('contract_end')
                    ->whereDate('contract_end', '>=', $today)
                    ->whereDate('contract_end', '<=', $thirtyDays);

            }


            // CONTRACT EXPIRED
            elseif ($contractExpiringFilter == 'expired') {

                $query->whereNotNull('contract_end')
                    ->whereDate('contract_end', '<', $today);

            }

        }


        // =========================================================
        // GET EMPLOYEE DATA
        // =========================================================

        $employees = $query
            ->orderBy('full_name')
            ->get();


        // =========================================================
        // SUMMARY
        // =========================================================

        $totalEmployees = $employees->count();


        // =========================================================
        // ACTIVE EMPLOYEE
        // =========================================================

        $activeEmployees = $employees
            ->where('status', 'active')
            ->count();


        // =========================================================
        // INACTIVE EMPLOYEE
        // =========================================================

        $inactiveEmployees = $employees
            ->where('status', 'inactive')
            ->count();


        // =========================================================
        // CONTRACT SUMMARY
        // =========================================================

        $today = Carbon::today();

        $contractExpiring = $employees->filter(function ($employee) use ($today) {

            if (!$employee->contract_end) {
                return false;
            }

            $contractEnd = Carbon::parse($employee->contract_end);

            $daysLeft = $today->diffInDays($contractEnd, false);

            return $daysLeft >= 0 && $daysLeft <= 30;

        })->count();


        // =========================================================
        // CONTRACT EXPIRED
        // =========================================================

        $contractExpired = $employees->filter(function ($employee) use ($today) {

            if (!$employee->contract_end) {
                return false;
            }

            $contractEnd = Carbon::parse($employee->contract_end);

            return $contractEnd->isBefore($today);

        })->count();


        // =========================================================
        // EMPLOYEE BY CLIENT
        // =========================================================

        $employeesByClient = $employees
            ->groupBy(function ($employee) {

                return $employee->client_id ?? 0;

            })
            ->map(function ($items) {

                return (object) [
                    'client' => $items->first()->client,
                    'total' => $items->count()
                ];

            })
            ->sortByDesc('total')
            ->values();


        // =========================================================
        // EMPLOYEE BY DIVISION
        // =========================================================

        $employeesByDivision = $employees
            ->groupBy(function ($employee) {

                return $employee->division ?? 'No Division';

            })
            ->map(function ($items, $divisionName) {

                return (object) [
                    'division' => $divisionName,
                    'total' => $items->count()
                ];

            })
            ->sortByDesc('total')
            ->values();


        // =========================================================
        // FILTER DATA
        // =========================================================

        $clients = Client::orderBy('name')->get();

        $divisions = Employee::query()
            ->whereNotNull('division')
            ->where('division', '!=', '')
            ->distinct()
            ->orderBy('division')
            ->pluck('division');


        // =========================================================
        // RETURN VIEW
        // =========================================================

        return view('pages.master.employee.index', [

            'employees' => $employees,

            // Summary
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'inactiveEmployees' => $inactiveEmployees,
            'contractExpiring' => $contractExpiring,
            'contractExpired' => $contractExpired,

            // Summary detail
            'employeesByClient' => $employeesByClient,
            'employeesByDivision' => $employeesByDivision,

            // Filter
            'clients' => $clients,
            'divisions' => $divisions,

            'clientId' => $clientId,
            'division' => $division,
            'status' => $status,

            // Contract filter
            'contractExpiringFilter' => $contractExpiringFilter,

            'type_menu' => 'master'
        ]);
    }

    public function create()
    {
        $clients = Client::all();

        return view('pages.master.employee.create', [
            'clients' => $clients,
            'type_menu' => 'master'
        ]);
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $clients = Client::all();

        return view('pages.master.employee.edit', [
            'employee' => $employee,
            'clients' => $clients,
            'type_menu' => 'master'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'required|unique:employees,employee_id',
            'full_name' => 'required',
            'nik_ktp' => 'required|unique:employees,nik_ktp',
            'phone' => ['required', 'digits_between:10,15'],
            'client_id' => 'required',
            'join_date' => 'required|date',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date|after:contract_start',
        ]);

        try {
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => bcrypt('password'),
                'role' => $request->role ?? 'employee',
                'employee_id' => $request->employee_id,
                'company' => $request->company,
                'is_active' => true
            ]);

            Employee::create([
                'user_id' => $user->id,
                'client_id' => $request->client_id,
                'employee_id' => $request->employee_id,
                'full_name' => $request->full_name,
                'nik_ktp' => $request->nik_ktp,
                'phone' => $request->phone,
                'email' => $request->email,
                'position' => $request->position,
                'division' => $request->division,
                'placement' => $request->placement,
                'join_date' => $request->join_date,
                'contract_start' => $request->contract_start,
                'contract_end' => $request->contract_end,
                'contract_extension_count' => $request->contract_extension_count ?? 0,
                'status' => $request->status ?? 'active',
                'absent_using_distance' => $request->has('absent_using_distance'),
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('employees.index')
                ->with('success', 'Employee successfully created');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        

        $employee = Employee::findOrFail($id);
        $user = User::findOrFail($employee->user_id);

        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,' . $employee->id,
            'full_name' => 'required',
            'nik_ktp' => 'required|unique:employees,nik_ktp,' . $employee->id,
            'phone' => ['required', 'digits_between:10,15'],
            'client_id' => 'required',
            'join_date' => 'required|date',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date|after:contract_start',
            'position' => 'required',
            'division' => 'required',
            'placement' => 'required',
            'status' => 'required',
            'role' => 'required',
        ]);
        try {
            // update user
            $user->update([
                'name' => $request->full_name,
                'role' => $request->role,
            ]);

            // update employee
            $data = $request->all();
            $data['absent_using_distance'] = $request->has('absent_using_distance');

            $employee->update($data);

            return redirect()
                ->route('employees.index')
                ->with('success', 'Updated successfully');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        User::where('id', $employee->user_id)->delete();

        $employee->delete();

        return back()->with('success', 'Deleted successfully');
    }

    public function resetFace($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->face_descriptor = null;
        $employee->save();

        return redirect()->back()->with('success', 'Face data has been reset!');
    }

    public function attendance($id)
    {
        $employee = Employee::findOrFail($id);

        $attendances = Attendance::where('employee_id', $id)
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.master.employee.attendance', [
            'employee' => $employee,
            'attendances' => $attendances,
            'type_menu' => 'master'
        ]);
    }

    public function updateAttendanceTime(Request $request, $id)
    {
        $request->validate([
            'check_in' => 'nullable',
            'check_out' => 'nullable',
        ]);

        $attendance = Attendance::findOrFail($id);

        $checkIn = $request->check_in;
        $checkOut = $request->check_out;

        $workingMinutes = null;

        if ($checkIn && $checkOut) {
            $workingMinutes = Carbon::parse($checkOut)
                ->diffInMinutes(Carbon::parse($checkIn));
        }

        $attendance->update([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'working_minutes' => $workingMinutes
        ]);

        return back()->with('success', 'Attendance updated!');
    }

    public function distanceSetting(Request $request)
    {
        $clientId = $request->client_id;
        $division = $request->division;

        $employees = Employee::with('client')
            ->when($clientId, function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })
            ->when($division, function ($q) use ($division) {
                $q->where('division', $division);
            })
            ->orderBy('full_name')
            ->get();

        $clients = Client::orderBy('name')->get();

        $divisions = Employee::select('division')
            ->whereNotNull('division')
            ->distinct()
            ->pluck('division');

        return view('pages.master.employee.distance-setting', [
            'employees' => $employees,
            'clients' => $clients,
            'divisions' => $divisions,
            'clientId' => $clientId,
            'division' => $division,
            'type_menu' => 'distance-setting',
        ]);
    }

    public function updateDistanceSetting(Request $request)
    {
        $employeeIds = $request->employee_ids ?? [];

        Employee::query()
            ->when($request->client_id, function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            })
            ->when($request->division, function ($q) use ($request) {
                $q->where('division', $request->division);
            })
            ->update([
                'absent_using_distance' => false
            ]);

        if (!empty($employeeIds)) {
            Employee::whereIn('id', $employeeIds)
                ->update([
                    'absent_using_distance' => true
                ]);
        }

        return back()->with(
            'success',
            'Distance setting updated successfully.'
        );
    }
    public function downloadTemplate()
    {
        $headers = [
            'employee_id',
            'full_name',
            'email',
            'role',
            'nik_ktp',
            'phone',
            'client_id',
            'position',
            'division',
            'placement',
            'join_date',
            'contract_start',
            'contract_end',
            'contract_extension_count',
            'status',
            'absent_using_distance',
            'notes'
        ];

        $filename = 'employee_template.csv';

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $headers);

            // contoh data
            fputcsv($file, [
                'EMP001',
                'John Doe',
                'john@example.com',
                'employee',
                '123456789',
                '08123456789',
                1,
                'Staff',
                'HRD',
                'HO',
                '2026-01-01',
                '2026-01-01',
                '2026-12-31',
                0,
                'active',
                1,
                'Example note'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt',
    ]);

    DB::beginTransaction();

    try {

        $file = fopen(
            $request->file('file')->getRealPath(),
            'r'
        );

        // Skip header
        fgetcsv($file);

        $inserted = 0;
        $line = 2; // karena header di baris 1

        while (($row = fgetcsv($file)) !== false) {

            if (count($row) < 17) {
                throw new \Exception(
                    "Invalid CSV format on row {$line}. Expected 17 columns, found " . count($row) . "."
                );
            }

            // Validasi data kosong
            if (
                empty($row[0]) ||
                empty($row[1]) ||
                empty($row[2]) ||
                empty($row[4]) ||
                empty($row[5])
            ) {
                throw new \Exception(
                    "Required data is missing on row {$line}."
                );
            }

            // Duplicate email
            if (User::where('email', $row[2])->exists()) {
                throw new \Exception(
                    "Duplicate email '{$row[2]}' found on row {$line}."
                );
            }

            // Duplicate employee id
            if (Employee::where('employee_id', $row[0])->exists()) {
                throw new \Exception(
                    "Employee ID '{$row[0]}' already exists on row {$line}."
                );
            }

            // Duplicate NIK
            if (Employee::where('nik_ktp', $row[4])->exists()) {
                throw new \Exception(
                    "NIK '{$row[4]}' already exists on row {$line}."
                );
            }

            // Client tidak ada
            if (!Client::find($row[6])) {
                throw new \Exception(
                    "Client ID '{$row[6]}' not found on row {$line}."
                );
            }

            $user = User::create([
                'name' => $row[1],
                'email' => $row[2],
                'password' => bcrypt('password'),
                'role' => $row[3] ?: 'employee',
                'employee_id' => $row[0],
                'company' => '-',
                'is_active' => true,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'client_id' => $row[6],
                'employee_id' => $row[0],
                'full_name' => $row[1],
                'nik_ktp' => $row[4],
                'phone' => $row[5],
                'email' => $row[2],
                'position' => $row[7],
                'division' => $row[8],
                'placement' => $row[9],
                'join_date' => $row[10],
                'contract_start' => $row[11],
                'contract_end' => $row[12],
                'contract_extension_count' => $row[13] ?: 0,
                'status' => $row[14] ?: 'active',
                'absent_using_distance' => (bool) $row[15],
                'notes' => $row[16],
            ]);

            $inserted++;
            $line++;
        }

        fclose($file);

        DB::commit();

        return back()->with(
            'success',
            "{$inserted} employee imported successfully."
        );

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error($e);

        return back()->withInput()->with(
            'error',
            $e->getMessage()
        );
    }
}
}
