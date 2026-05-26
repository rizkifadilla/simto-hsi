@extends('layouts.app')

@section('title', 'Absence history')

@push('style')
<link rel="stylesheet"
    href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>My Attendance</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">My Attendance</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">My Attendance</h2>
            <p class="section-lead">
                attendance history data
            </p>

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-header">
                            <h4>Download Timesheet</h4>
                        </div>

                        <div class="card-body">
                            <form method="GET" action="{{ route('attendance.export') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="date" name="from" class="form-control" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="date" name="to" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success">
                                            <i class="fas fa-download"></i> Download Excel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">

                        <div class="card-header">
                            <h4>Attendance Data</h4>
                        </div>

                        <div class="card-body">

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table-striped table" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Check In</th>
                                            <th>Check Out</th>
                                            <th>Deration</th>
                                            <th>Location</th>
                                            <th>Face</th>
                                            <th>Task</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($attendances as $key => $row)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                            </td>

                                            <td>
                                                {{ $row->check_in ?? '-' }} <br>
                                                <small class="text-muted">
                                                    {{ $row->check_in_lat }},
                                                    {{ $row->check_in_long }}
                                                </small>
                                            </td>

                                            <td>
                                                {{ $row->check_out ?? '-' }} <br>
                                                <small class="text-muted">
                                                    {{ $row->check_out_lat }},
                                                    {{ $row->check_out_long }}
                                                </small>
                                            </td>

                                            <td>
                                                @if($row->working_minutes)
                                                    {{ floor($row->working_minutes / 60) }}j 
                                                    {{ $row->working_minutes % 60 }}m
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>
                                                @if($row->is_within_radius)
                                                    <span class="badge badge-success">Valid</span>
                                                @else
                                                    <span class="badge badge-danger">Outside</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($row->is_face_valid)
                                                    <span class="badge badge-success">Valid</span>
                                                @else
                                                    <span class="badge badge-danger">Invalid</span>
                                                @endif
                                            </td>

                                            <td style="max-width:200px;">
                                                {{ $row->task ?? '-' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

<!-- 🔥 WAJIB (biar sama seperti template kamu) -->
<script src="{{ asset('js/page/modules-datatables.js') }}"></script>
@endpush