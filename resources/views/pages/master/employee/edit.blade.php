@extends('layouts.app')

@section('title', 'Edit Employee')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Employee</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">

                        <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                <!-- LEFT -->
                                <div class="col-md-6">

                                    <h6>Data User</h6>

                                    <div class="form-group">
                                        <label>Email Login</label>
                                        <input type="email" name="email" value="{{ $employee->user->email }}"
                                            class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" class="form-control">
                                            <option value="employee" {{ $employee->user->role == 'employee' ? 'selected' : '' }}>Employee</option>
                                            <option value="talent acquisition" {{ $employee->user->role == 'talent acquisition' ? 'selected' : '' }}>Talent Acquisition</option>
                                            <option value="admin" {{ $employee->user->role == 'admin' ? 'selected' : '' }}>
                                                Admin</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Company</label>
                                        <input type="text" name="company" value="{{ $employee->user->company }}"
                                            class="form-control">
                                    </div>

                                    <h6>Data Employee</h6>

                                    <div class="form-group">
                                        <label>Employee ID</label>
                                        <input type="text" name="employee_id" value="{{ $employee->employee_id }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="full_name" value="{{ $employee->full_name }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>NIK KTP</label>
                                        <input type="text" name="nik_ktp" value="{{ $employee->nik_ktp }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" name="phone" value="{{ $employee->phone }}" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Email Employee</label>
                                        <input type="email" name="employee_email" value="{{ $employee->email }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Absence Using Distance (GPS)</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                name="absent_using_distance"
                                                value="1"
                                                class="custom-control-input"
                                                id="absent_using_distance"
                                                {{ $employee->absent_using_distance ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="absent_using_distance">
                                                Enable Location Based Attendance
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <!-- RIGHT -->
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label>Client</label>
                                        <select name="client_id" class="form-control">
                                            @foreach($clients as $c)
                                                <option value="{{ $c->id }}" {{ $employee->client_id == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Position</label>
                                        <input type="text" name="position" value="{{ $employee->position }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Division</label>
                                        <input type="text" name="division" value="{{ $employee->division }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Placement</label>
                                        <input type="text" name="placement" value="{{ $employee->placement }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Join Date</label>
                                        <input type="date" name="join_date" value="{{ $employee->join_date }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Contract Start</label>
                                        <input type="date" name="contract_start" value="{{ $employee->contract_start }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Contract End</label>
                                        <input type="date" name="contract_end" value="{{ $employee->contract_end }}"
                                            class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Extension Count</label>
                                        <input type="number" name="contract_extension_count"
                                            value="{{ $employee->contract_extension_count }}" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control">{{ $employee->notes }}</textarea>
                                    </div>

                                </div>

                            </div>

                            <button class="btn btn-primary">Update</button>

                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection