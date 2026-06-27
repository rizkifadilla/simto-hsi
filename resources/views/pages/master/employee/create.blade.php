@extends('layouts.app')

@section('title', 'Add Employee')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Add Employee</h1>
            </div>

            <div class="section-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="card">
                    <div class="card-body">

                        <form method="POST" action="{{ route('employees.store') }}">
                            @csrf

                            <div class="row">
                                <!-- LEFT -->
                                <div class="col-md-6">

                                    <h6>Data User</h6>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            value="{{ old('email') }}"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" class="form-control">
                                            <option value="employee"
                                                {{ old('role') == 'employee' ? 'selected' : '' }}>
                                                Employee
                                            </option>

                                            <option value="talent acquisition"
                                                {{ old('role') == 'talent acquisition' ? 'selected' : '' }}>
                                                Talent Acquisition
                                            </option>

                                            <option value="admin"
                                                {{ old('role') == 'admin' ? 'selected' : '' }}>
                                                Admin
                                            </option>
                                        </select>
                                    </div>

                                    <!-- <div class="form-group">
                                        <label>Company</label>
                                        <input type="text" name="company" class="form-control">
                                    </div> -->


                                    <h6>Data Employee</h6>

                                    <div class="form-group">
                                        <label>Employee ID</label>
                                        <input
                                            type="text"
                                            name="employee_id"
                                            class="form-control"
                                            value="{{ old('employee_id') }}"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input
                                            type="text"
                                            name="full_name"
                                            class="form-control"
                                            value="{{ old('full_name') }}"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label>NIK KTP</label>
                                        <input
                                            type="text"
                                            name="nik_ktp"
                                            class="form-control"
                                            value="{{ old('nik_ktp') }}"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input
                                            type="text"
                                            name="phone"
                                            class="form-control"
                                            value="{{ old('phone') }}"
                                            inputmode="numeric"
                                            pattern="[0-9]*"
                                            maxlength="15"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>

                                    <!-- <div class="form-group">
                                        <label>Email Employee</label>
                                        <input type="email" name="email" class="form-control">
                                    </div> -->

                                    <div class="form-group">
                                        <label>Absence Using Distance (GPS)</label>
                                        <div class="custom-control custom-checkbox">
                                            <input
                                                type="checkbox"
                                                name="absent_using_distance"
                                                value="1"
                                                class="custom-control-input"
                                                id="absent_using_distance"
                                                {{ old('absent_using_distance') ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="absent_using_distance">
                                                Enable Location Based Attendance
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <!-- RIGHT -->
                                <div class="col-md-6">

                                    <div class="form-group mt-4">
                                        <label>Client</label>
                                        <select name="client_id" class="form-control">
                                            @foreach($clients as $c)
                                                <option
                                                    value="{{ $c->id }}"
                                                    {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Position</label>
                                        <select name="position" class="form-control">
                                            <option value="Staff"
                                                {{ old('position') == 'Staff' ? 'selected' : '' }}>
                                                Staff
                                            </option>

                                            <option value="Senior Staff"
                                                {{ old('position') == 'Senior Staff' ? 'selected' : '' }}>
                                                Senior Staff
                                            </option>

                                            <option value="Supervisor"
                                                {{ old('position') == 'Supervisor' ? 'selected' : '' }}>
                                                Supervisor
                                            </option>

                                            <option value="Coordinator"
                                                {{ old('position') == 'Coordinator' ? 'selected' : '' }}>
                                                Coordinator
                                            </option>

                                            <option value="Team Leader"
                                                {{ old('position') == 'Team Leader' ? 'selected' : '' }}>
                                                Team Leader
                                            </option>

                                            <option value="Manager"
                                                {{ old('position') == 'Manager' ? 'selected' : '' }}>
                                                Manager
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Division</label>
                                        <select name="division" class="form-control">
                                            <option value="HRD"
                                                {{ old('division') == 'HRD' ? 'selected' : '' }}>
                                                HRD
                                            </option>

                                            <option value="Operational"
                                                {{ old('division') == 'Operational' ? 'selected' : '' }}>
                                                Operational
                                            </option>

                                            <option value="Finance"
                                                {{ old('division') == 'Finance' ? 'selected' : '' }}>
                                                Finance
                                            </option>

                                            <option value="Marketing"
                                                {{ old('division') == 'Marketing' ? 'selected' : '' }}>
                                                Marketing
                                            </option>

                                            <option value="IT"
                                                {{ old('division') == 'IT' ? 'selected' : '' }}>
                                                IT
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Placement</label>
                                        <select name="placement" class="form-control">
                                            <option value="HO"
                                                {{ old('placement') == 'HO' ? 'selected' : '' }}>
                                                HO
                                            </option>

                                            <option value="Client"
                                                {{ old('placement') == 'Client' ? 'selected' : '' }}>
                                                Client
                                            </option>

                                            <option value="Project"
                                                {{ old('placement') == 'Project' ? 'selected' : '' }}>
                                                Project
                                            </option>

                                            <option value="Warehouse"
                                                {{ old('placement') == 'Warehouse' ? 'selected' : '' }}>
                                                Warehouse
                                            </option>

                                            <option value="Remote"
                                                {{ old('placement') == 'Remote' ? 'selected' : '' }}>
                                                Remote / WFH
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Join Date</label>
                                        <input
                                            type="date"
                                            name="join_date"
                                            class="form-control"
                                            value="{{ old('join_date') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Contract Start</label>
                                        <input
                                            type="date"
                                            name="contract_start"
                                            class="form-control"
                                            value="{{ old('contract_start') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Contract End</label>
                                        <input
                                            type="date"
                                            name="contract_end"
                                            class="form-control"
                                            value="{{ old('contract_end') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Extension Count</label>
                                        <input
                                            type="number"
                                            name="contract_extension_count"
                                            class="form-control"
                                            value="{{ old('contract_extension_count') }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active"
                                                {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>

                                            <option value="inactive"
                                                {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea
                                            name="notes"
                                            class="form-control">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <button class="btn btn-primary">Save</button>

                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection