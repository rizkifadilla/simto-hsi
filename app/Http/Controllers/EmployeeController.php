<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Client;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('client')->get();

        return view('pages.master.employee.index', [
            'employees' => $employees,
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
            // USER
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|min:6',

            // EMPLOYEE
            'employee_id' => 'required|unique:employees,employee_id',
            'full_name' => 'required',
            'nik_ktp' => 'required|unique:employees,nik_ktp',
            'phone' => 'required',
            'client_id' => 'required',
            'join_date' => 'required|date',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date|after:contract_start',
        ]);

        // 🔥 CREATE USER
        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => bcrypt($request->password ?? 'password'),
            'role' => $request->role ?? 'employee',
            'employee_id' => $request->employee_id,
            'company' => $request->company,
            'is_active' => true
        ]);

        // 🔥 CREATE EMPLOYEE
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

        return redirect()->route('employees.index')->with('success', 'Employee successfully created');
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $user = User::findOrFail($employee->user_id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'employee_id' => 'required|unique:employees,employee_id,' . $employee->id,
            'nik_ktp' => 'required|unique:employees,nik_ktp,' . $employee->id,
        ]);

        // update user
        $user->update([
            'name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
            'company' => $request->company,
        ]);

        // update employee
        $data = $request->all();
        $data['absent_using_distance'] = $request->has('absent_using_distance');

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Updated successfully');
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
}
