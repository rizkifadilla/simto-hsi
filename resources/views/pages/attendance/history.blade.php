@extends('layouts.app')

@section('title', 'Absence History')

@push('style')

<link rel="stylesheet"
      href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">

<style>

    /* =========================================================
       SUMMARY
    ========================================================= */

    .attendance-summary-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
        height: 100%;
    }

    .attendance-summary-card .card-body {
        padding: 20px;
    }

    .summary-title {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .summary-number {
        font-size: 26px;
        font-weight: 700;
        line-height: 1.2;
    }

    .summary-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 20px;
    }

    .summary-total {
        background: #e8f1ff;
        color: #6777ef;
    }

    .summary-present {
        background: #e8f8ef;
        color: #28a745;
    }

    .summary-permit {
        background: #e8f6fb;
        color: #17a2b8;
    }

    .summary-leave {
        background: #fff4df;
        color: #f39c12;
    }

    .summary-sick {
        background: #fceaea;
        color: #dc3545;
    }

    .summary-absent {
        background: #fde8e8;
        color: #dc3545;
    }


    /* =========================================================
       TABLE
    ========================================================= */

    .attendance-table th,
    .attendance-table td {
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .attendance-table td.task-column {
        white-space: normal;
        max-width: 250px;
    }


    /* =========================================================
       FILTER
    ========================================================= */

    .filter-card {
        border-radius: 10px;
    }


    /* =========================================================
       ABSENT ROW
    ========================================================= */

    .absent-row {
        background-color: #fde8e8 !important;
    }

    .weekend-row {
        background-color: #f2f2f2 !important;
    }


    /* =========================================================
       LOADING
    ========================================================= */

    #loadingOverlay {
        position: fixed;
        inset: 0;

        background: rgba(255,255,255,.7);

        z-index: 99999;

        display: none;

        align-items: center;
        justify-content: center;

        backdrop-filter: blur(2px);
    }

    .loading-box {
        background: white;

        padding: 30px 40px;

        border-radius: 16px;

        box-shadow: 0 10px 30px rgba(0,0,0,.1);

        text-align: center;
    }

</style>

@endpush


@section('main')

<div class="main-content">

<section class="section">

    {{-- =========================================================
         HEADER
    ========================================================= --}}

    <div class="section-header">

        <h1>My Attendance</h1>

        <div class="section-header-breadcrumb">

            <div class="breadcrumb-item">
                My Attendance
            </div>

        </div>

    </div>


    <div class="section-body">

        <h2 class="section-title">
            My Attendance
        </h2>

        <p class="section-lead">
            Attendance history and attendance summary
        </p>


        {{-- =====================================================
             SUMMARY
        ===================================================== --}}

        <div class="row">

            {{-- TOTAL --}}

            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">

                <div class="card attendance-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Total Attendance
                                </div>

                                <div class="summary-number">
                                    {{ $totalDays }}
                                </div>

                            </div>

                            <div class="summary-icon summary-total">

                                <i class="fas fa-calendar-check"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PRESENT --}}

            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">

                <div class="card attendance-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Present
                                </div>

                                <div class="summary-number text-success">
                                    {{ $totalPresent }}
                                </div>

                            </div>

                            <div class="summary-icon summary-present">

                                <i class="fas fa-user-check"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PERMIT --}}

            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">

                <div class="card attendance-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Permit
                                </div>

                                <div class="summary-number text-info">
                                    {{ $totalPermit }}
                                </div>

                            </div>

                            <div class="summary-icon summary-permit">

                                <i class="fas fa-file-alt"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- LEAVE --}}

            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">

                <div class="card attendance-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Leave
                                </div>

                                <div class="summary-number text-warning">
                                    {{ $totalLeave }}
                                </div>

                            </div>

                            <div class="summary-icon summary-leave">

                                <i class="fas fa-plane"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- SICK --}}

            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">

                <div class="card attendance-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Sick
                                </div>

                                <div class="summary-number text-danger">
                                    {{ $totalSick }}
                                </div>

                            </div>

                            <div class="summary-icon summary-sick">

                                <i class="fas fa-medkit"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ABSENT --}}

            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">

                <div class="card attendance-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Absent
                                </div>

                                <div class="summary-number text-danger">
                                    {{ $totalAbsent }}
                                </div>

                            </div>

                            <div class="summary-icon summary-absent">

                                <i class="fas fa-user-times"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             DOWNLOAD TIMESHEET
        ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h4>
                    Download Timesheet
                </h4>

            </div>

            <div class="card-body">

                <form method="GET"
                    class="mb-3">

                    <div class="row">

                        <div class="col-md-4">

                            <label>
                                From
                            </label>

                            <input type="date"
                                name="from"
                                value="{{ request('from') }}"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-4">

                            <label>
                                To
                            </label>

                            <input type="date"
                                name="to"
                                value="{{ request('to') }}"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-4 d-flex align-items-end">

                            {{-- EXCEL --}}

                            <button type="submit"
                                    formaction="{{ route('attendance.export') }}"
                                    class="btn btn-success mr-2">

                                <i class="fas fa-file-excel"></i>

                                Excel

                            </button>


                            {{-- PDF --}}

                            <button type="submit"
                                    formaction="{{ route('attendance.export.pdf') }}"
                                    formtarget="_blank"
                                    class="btn btn-danger">

                                <i class="fas fa-file-pdf"></i>

                                PDF

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        {{-- =====================================================
             ATTENDANCE DATA
        ===================================================== --}}

        <div class="card filter-card">

            <div class="card-header">

                <h4>
                    Attendance Data
                </h4>

            </div>


            <div class="card-body">


                {{-- SUCCESS --}}

                @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show">

                        {{ session('success') }}

                        <button type="button"
                                class="close"
                                data-dismiss="alert">

                            <span>&times;</span>

                        </button>

                    </div>

                @endif


                {{-- =================================================
                     FILTER
                ================================================= --}}

                <form method="GET"
                      action="{{ route('attendance.my') }}"
                      id="filterForm"
                      class="row mb-4">


                    {{-- MONTH --}}

                    <div class="col-md-2">

                        <label>
                            Month
                        </label>

                        <input type="month"
                               name="month"
                               value="{{ $month ?? now()->format('Y-m') }}"
                               class="form-control">

                    </div>


                    {{-- FROM --}}

                    <div class="col-md-2">

                        <label>
                            From
                        </label>

                        <input type="date"
                               name="from"
                               value="{{ $from }}"
                               class="form-control">

                    </div>


                    {{-- TO --}}

                    <div class="col-md-2">

                        <label>
                            To
                        </label>

                        <input type="date"
                               name="to"
                               value="{{ $to }}"
                               class="form-control">

                    </div>


                    {{-- STATUS --}}

                    <div class="col-md-2">

                        <label>
                            Status
                        </label>

                        <select name="status"
                                class="form-control">

                            <option value="">
                                All Status
                            </option>

                            <option value="hadir"
                                {{ $status == 'hadir' ? 'selected' : '' }}>

                                Present

                            </option>

                            <option value="izin"
                                {{ $status == 'izin' ? 'selected' : '' }}>

                                Permit

                            </option>

                            <option value="cuti"
                                {{ $status == 'cuti' ? 'selected' : '' }}>

                                Leave

                            </option>

                            <option value="sakit"
                                {{ $status == 'sakit' ? 'selected' : '' }}>

                                Sick

                            </option>

                            <option value="absent"
                                {{ $status == 'absent' ? 'selected' : '' }}>

                                Absent

                            </option>

                        </select>

                    </div>


                    {{-- LOCATION --}}

                    <div class="col-md-2">

                        <label>
                            Location
                        </label>

                        <select name="location"
                                class="form-control">

                            <option value="">
                                All Location
                            </option>

                            <option value="valid"
                                {{ $location == 'valid' ? 'selected' : '' }}>

                                Valid

                            </option>

                            <option value="outside"
                                {{ $location == 'outside' ? 'selected' : '' }}>

                                Outside

                            </option>

                        </select>

                    </div>


                    {{-- FACE --}}

                    <div class="col-md-2">

                        <label>
                            Face
                        </label>

                        <select name="face"
                                class="form-control">

                            <option value="">
                                All Face
                            </option>

                            <option value="valid"
                                {{ $face == 'valid' ? 'selected' : '' }}>

                                Valid

                            </option>

                            <option value="invalid"
                                {{ $face == 'invalid' ? 'selected' : '' }}>

                                Invalid

                            </option>

                        </select>

                    </div>


                    {{-- BUTTON --}}

                    <div class="col-md-12 mt-3">

                        <button type="submit"
                                class="btn btn-primary mr-2">

                            <i class="fas fa-filter"></i>

                            Filter

                        </button>


                        <a href="{{ route('attendance.my') }}"
                           class="btn btn-secondary">

                            <i class="fas fa-redo"></i>

                            Reset

                        </a>

                    </div>

                </form>


                {{-- =================================================
                     FILTER INFO
                ================================================= --}}

                @if($month)

                    <div class="alert alert-light border mb-3">

                        <i class="fas fa-calendar-alt mr-1"></i>

                        Showing attendance for

                        <strong>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                        </strong>

                    </div>

                @endif


                {{-- =================================================
                     LOADING
                ================================================= --}}

                <div id="loadingOverlay">

                    <div class="loading-box">

                        <div class="spinner-border text-primary mb-3">
                        </div>

                        <h6>
                            Loading attendance data...
                        </h6>

                        <small class="text-muted">
                            Please Wait
                        </small>

                    </div>

                </div>


                {{-- =================================================
                     TABLE
                ================================================= --}}

                <div class="table-responsive">

                    <table class="table-striped table attendance-table"
                           id="table-1">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Date</th>

                                <th>Check In</th>

                                <th>Check Out</th>

                                <th>Duration</th>

                                <th>Location</th>

                                <th>Face</th>

                                <th>Status</th>

                                <th>Task</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($attendances as $key => $row)

                                @php

                                    $isAbsent = $row->is_absent ?? false;

                                @endphp

                                <tr class="{{ $isAbsent ? 'absent-row' : '' }}">

                                    {{-- NO --}}

                                    <td>
                                        {{ $key + 1 }}
                                    </td>


                                    {{-- DATE --}}

                                    <td>

                                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}

                                        <br>

                                        <small class="text-muted">

                                            {{ \Carbon\Carbon::parse($row->date)->translatedFormat('l') }}

                                        </small>

                                    </td>


                                    {{-- CHECK IN --}}

                                    <td>

                                        @if($isAbsent)

                                            -

                                        @else

                                            {{ $row->check_in ?? '-' }}

                                            <br>

                                            <small class="text-muted">

                                                {{ $row->check_in_lat ?? '-' }},
                                                {{ $row->check_in_long ?? '-' }}

                                            </small>

                                        @endif

                                    </td>


                                    {{-- CHECK OUT --}}

                                    <td>

                                        @if($isAbsent)

                                            -

                                        @else

                                            {{ $row->check_out ?? '-' }}

                                            <br>

                                            <small class="text-muted">

                                                {{ $row->check_out_lat ?? '-' }},
                                                {{ $row->check_out_long ?? '-' }}

                                            </small>

                                        @endif

                                    </td>


                                    {{-- DURATION --}}

                                    <td>

                                        @if(!$isAbsent && $row->working_minutes)

                                            {{ floor($row->working_minutes / 60) }}j
                                            {{ $row->working_minutes % 60 }}m

                                        @else

                                            -

                                        @endif

                                    </td>


                                    {{-- LOCATION --}}

                                    <td>

                                        @if($isAbsent)

                                            -

                                        @elseif($row->is_within_radius)

                                            <span class="badge badge-success">
                                                Valid
                                            </span>

                                        @else

                                            <span class="badge badge-danger">
                                                Outside
                                            </span>

                                        @endif

                                    </td>


                                    {{-- FACE --}}

                                    <td>

                                        @if($isAbsent)

                                            -

                                        @elseif($row->is_face_valid)

                                            <span class="badge badge-success">
                                                Valid
                                            </span>

                                        @else

                                            <span class="badge badge-danger">
                                                Invalid
                                            </span>

                                        @endif

                                    </td>


                                    {{-- STATUS --}}

                                    <td>

                                        @if($isAbsent)

                                            <span class="badge badge-danger">
                                                Absent
                                            </span>

                                        @elseif($row->task === 'izin')

                                            <span class="badge badge-info">
                                                Permit
                                            </span>

                                        @elseif($row->task === 'cuti')

                                            <span class="badge badge-warning">
                                                Leave
                                            </span>

                                        @elseif($row->task === 'sakit')

                                            <span class="badge badge-danger">
                                                Sick
                                            </span>

                                        @elseif($row->check_in)

                                            <span class="badge badge-success">
                                                Present
                                            </span>

                                        @else

                                            <span class="badge badge-danger">
                                                Absent
                                            </span>

                                        @endif

                                    </td>


                                    {{-- TASK --}}

                                    <td class="task-column">

                                        @if($isAbsent)

                                            <span class="text-danger">
                                                Absent
                                            </span>

                                        @else

                                            {{ $row->task ?? '-' }}

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

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

<script src="{{ asset('js/page/modules-datatables.js') }}"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const filterForm = document.getElementById('filterForm');

    const loadingOverlay = document.getElementById('loadingOverlay');

    if (!filterForm) {
        return;
    }

    filterForm.addEventListener('submit', function () {

        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }

    });

});

</script>

@endpush