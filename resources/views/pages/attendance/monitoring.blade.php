@extends('layouts.app')

@section('title', 'Attendance Monitoring')

@section('main')
<div class="main-content">
<section class="section">

    <div class="section-header">
        <h1>Attendance Monitoring</h1>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- FILTER --}}
            <form method="GET" class="row mb-4" id="filterForm">

                <div class="col-md-2">
                    <label>From</label>

                    <input type="date"
                            name="from"
                            value="{{ $from }}"
                            class="form-control">
                </div>

                <div class="col-md-2">
                    <label>To</label>

                    <input type="date"
                            name="to"
                            value="{{ $to }}"
                            class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Client</label>

                    <select name="client_id" class="form-control">
                        <option value="">All Client</option>

                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ $clientId == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Division</label>

                    <select name="division" class="form-control">
                        <option value="">All Division</option>

                        @foreach($divisions as $div)
                            <option value="{{ $div }}"
                                {{ $division == $div ? 'selected' : '' }}>
                                {{ $div }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100 mr-2">
                        <span class="normal-text">
                            Filter
                        </span>
                    </button>

                    <a href="{{ route('attendance.monitoring.export', request()->query()) }}"
                        class="btn btn-success">
                            Download
                    </a>
                </div>

            </form>

            <div id="loadingOverlay" style="display:none;">
                <div class="loading-box">
                    <div class="spinner-border text-primary mb-3"></div>

                    <h6>Loading attendance...</h6>

                    <small class="text-muted">
                        Please Wait
                    </small>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-bordered table-sm attendance-table">

                    <thead>

                        <tr>

                            <th class="sticky-col bg-white"
                                style="min-width:220px;">
                                Employee
                            </th>

                            @foreach($dates as $date)

                                <th class="text-center"
                                    style="min-width:80px;">

                                    {{ $date->format('d') }}

                                    <br>

                                    <small>
                                        {{ $date->translatedFormat('D') }}
                                    </small>

                                </th>

                            @endforeach

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($employees as $employee)

                            <tr>

                                <td class="sticky-col bg-white">

                                    <div>
                                        <strong>
                                            {{ $employee->full_name }}
                                        </strong>
                                    </div>

                                    <small class="text-muted">
                                        {{ $employee->division }}
                                    </small>

                                </td>

                                @foreach($dates as $date)

                                    @php
                                        $attendance = $employee->attendances
                                            ->where('date', $date->format('Y-m-d'))
                                            ->first();

                                        $minutes = $attendance->working_minutes ?? 0;

                                        $hours = floor($minutes / 60);
                                        $remainingMinutes = $minutes % 60;

                                        $tooltip = '';

                                        if ($attendance) {

                                            $tooltip .= 'Check In : '
                                                . ($attendance->check_in ?? '-') . "\n";

                                            $tooltip .= 'Check Out : '
                                                . ($attendance->check_out ?? '-') . "\n";

                                            $tooltip .= 'Task : '
                                                . ($attendance->task ?? '-') . "\n";

                                            $tooltip .= 'Working : '
                                            . $hours . ' h '
                                            . $remainingMinutes . ' m';
                                        }
                                    @endphp

                                    <td class="text-center attendance-cell"
                                        title="{{ $tooltip }}">

                                        @if(!$attendance)
                                                ❌

                                        @elseif($attendance->task == 'izin')

                                            <span class="badge badge-info">
                                                Permit
                                            </span>

                                        @elseif($attendance->task == 'cuti')

                                            <span class="badge badge-info">
                                                Leave
                                            </span>

                                        @elseif($attendance->task == 'sakit')

                                            <span class="badge badge-warning">
                                                Sick
                                            </span>

                                        @elseif($attendance->check_in)
                                                ✅

                                        @else

                                            <span class="badge badge-secondary">
                                                -
                                            </span>

                                        @endif

                                    </td>

                                @endforeach

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</section>
</div>
@endsection

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const filterForm = document.getElementById('filterForm');

    if (!filterForm) return;

    filterForm.addEventListener('submit', function () {

        console.log("CLICK");

        document.getElementById('loadingOverlay').style.display = 'flex';

    });

});

</script>

@endpush

@push('style')

<style>

.attendance-table th,
.attendance-table td {
    vertical-align: middle !important;
    white-space: nowrap;
}

/* =========================
   STICKY EMPLOYEE COLUMN
========================= */
.sticky-col {
    position: sticky;
    left: 0;
    z-index: 5;
    background: #fff !important;
}

/* HEADER STICKY */
.attendance-table thead th {
    position: sticky;
    top: 0;
    z-index: 4;
    background: #fff;
}

/* HEADER EMPLOYEE PALING ATAS */
.attendance-table thead .sticky-col {
    z-index: 10;
    background: #fff !important;
}

/* BODY EMPLOYEE */
.attendance-table tbody .sticky-col {
    background: #fff !important;
}

/* HOVER */
.attendance-cell {
    cursor: pointer;
    transition: .2s;
}

.attendance-cell:hover {
    background: #f5f5f5;
    transform: scale(1.05);
}

#loadingOverlay {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,.7);
    z-index: 99999;

    display: flex;
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

.attendance-table th,
.attendance-table td {
    vertical-align: middle !important;
    white-space: nowrap;
}

.sticky-col {
    position: sticky;
    left: 0;
    z-index: 2;
}

.attendance-table thead th {
    position: sticky;
    top: 0;
    z-index: 3;
}

.attendance-cell {
    cursor: pointer;
    transition: .2s;
}

.attendance-cell:hover {
    background: #f5f5f5;
    transform: scale(1.05);
}

</style>

@endpush