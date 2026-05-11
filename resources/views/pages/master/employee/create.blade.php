@extends('layouts.app')

@section('title', 'Add Employee')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Add Employee</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">

                        <form method="POST" action="{{ route('employees.store') }}">
                            @csrf

                            <div class="row">

                                <!-- LEFT -->
                                <div class="col-md-6">

                                    <h6>DATA USER</h6>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control">
                                        <small>Default: password</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" class="form-control">
                                            <option value="employee">Employee</option>
                                            <option value="talent acquisition">Talent Acquisition</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Company</label>
                                        <input type="text" name="company" class="form-control">
                                    </div>

                                    <hr>

                                    <h6>DATA Employee</h6>

                                    <div class="form-group">
                                        <label>Employee ID</label>
                                        <input type="text" name="employee_id" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>NIK KTP</label>
                                        <input type="text" name="nik_ktp" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" name="phone" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Email Employee</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Absensi Pakai Jarak (GPS)</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                name="absent_using_distance"
                                                value="1"
                                                class="custom-control-input"
                                                id="absent_using_distance">
                                            <label class="custom-control-label"
                                                for="absent_using_distance">
                                                Aktifkan Absensi Berbasis Lokasi
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
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Position</label>
                                        <input type="text" name="position" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Division</label>
                                        <input type="text" name="division" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Placement</label>
                                        <input type="text" name="placement" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Join Date</label>
                                        <input type="date" name="join_date" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Contract Start</label>
                                        <input type="date" name="contract_start" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Contract End</label>
                                        <input type="date" name="contract_end" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Extension Count</label>
                                        <input type="number" name="contract_extension_count" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control"></textarea>
                                    </div>
                                </div>

                            </div>

                            <button class="btn btn-primary">Simpan</button>

                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection