@extends('layouts.app')

@section('title', 'Employee')

@push('style')

<link rel="stylesheet"
      href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">

<style>

    /* =========================================================
       SUMMARY CARD
    ========================================================= */

    .employee-summary-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
        height: 100%;
    }

    .employee-summary-card .card-body {
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

    .summary-active {
        background: #e8f8ef;
        color: #28a745;
    }

    .summary-inactive {
        background: #fceaea;
        color: #dc3545;
    }

    .summary-warning {
        background: #fff4df;
        color: #f39c12;
    }

    .summary-danger {
        background: #fde8e8;
        color: #dc3545;
    }


    /* =========================================================
       FILTER
    ========================================================= */

    .filter-card {
        border-radius: 10px;
    }


    /* =========================================================
       CLIENT / DIVISION SUMMARY
    ========================================================= */

    .summary-list {
        max-height: 130px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .summary-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 8px 0;

        border-bottom: 1px solid #eee;
    }

    .summary-list-item:last-child {
        border-bottom: none;
    }

    .summary-list-name {
        max-width: 80%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /* =========================================================
       TABLE
    ========================================================= */

    .employee-table th,
    .employee-table td {
        vertical-align: middle !important;
        white-space: nowrap;
    }


    /* =========================================================
       LOADING OVERLAY
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

        <h1>Master Employee</h1>

        <div class="section-header-breadcrumb">

            <div class="breadcrumb-item active">
                <a href="#">Master</a>
            </div>

            <div class="breadcrumb-item">
                Employee
            </div>

        </div>

    </div>


    {{-- =========================================================
         SECTION DESCRIPTION
    ========================================================= --}}

    <div class="section-body">

        <h2 class="section-title">
            Employee Management
        </h2>

        <p class="section-lead">
            Manage employee information, status, client,
            division and contract information.
        </p>


        {{-- =====================================================
             SUMMARY CARDS
        ===================================================== --}}

        <div class="row">

            {{-- TOTAL EMPLOYEES --}}
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">

                <div class="card employee-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Total Employees
                                </div>

                                <div class="summary-number">
                                    {{ $totalEmployees }}
                                </div>

                            </div>

                            <div class="summary-icon summary-total">
                                <i class="fas fa-users"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ACTIVE EMPLOYEES --}}
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">

                <div class="card employee-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Active Employees
                                </div>

                                <div class="summary-number text-success">
                                    {{ $activeEmployees }}
                                </div>

                            </div>

                            <div class="summary-icon summary-active">
                                <i class="fas fa-user-check"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- INACTIVE EMPLOYEES --}}
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">

                <div class="card employee-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Inactive Employees
                                </div>

                                <div class="summary-number text-danger">
                                    {{ $inactiveEmployees }}
                                </div>

                            </div>

                            <div class="summary-icon summary-inactive">
                                <i class="fas fa-user-times"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- CONTRACT EXPIRING --}}
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">

                <div class="card employee-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Contract Expiring
                                </div>

                                <div class="summary-number text-warning">
                                    {{ $contractExpiring }}
                                </div>

                                <small class="text-muted">
                                    Within 30 days
                                </small>

                            </div>

                            <div class="summary-icon summary-warning">
                                <i class="fas fa-hourglass-half"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             SECOND SUMMARY ROW
        ===================================================== --}}

        <div class="row">

            {{-- CONTRACT EXPIRED --}}
            <div class="col-lg-3 col-md-6 mb-4">

                <div class="card employee-summary-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="summary-title">
                                    Contract Expired
                                </div>

                                <div class="summary-number text-danger">
                                    {{ $contractExpired }}
                                </div>

                            </div>

                            <div class="summary-icon summary-danger">
                                <i class="fas fa-calendar-times"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- EMPLOYEES BY CLIENT --}}
            <div class="col-lg-5 col-md-6 mb-4">

                <div class="card employee-summary-card">

                    <div class="card-header">

                        <h4>
                            Employees by Client
                        </h4>

                    </div>

                    <div class="card-body">

                        <div class="summary-list">

                            @forelse($employeesByClient as $item)

                                <div class="summary-list-item">

                                    <span class="summary-list-name"
                                          title="{{ $item->client->name ?? 'No Client' }}">

                                        {{ $item->client->name ?? 'No Client' }}

                                    </span>

                                    <span class="badge badge-primary">

                                        {{ $item->total }}

                                    </span>

                                </div>

                            @empty

                                <div class="text-muted text-center">

                                    No client data

                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>


            {{-- EMPLOYEES BY DIVISION --}}
            <div class="col-lg-4 col-md-12 mb-4">

                <div class="card employee-summary-card">

                    <div class="card-header">

                        <h4>
                            Employees by Division
                        </h4>

                    </div>

                    <div class="card-body">

                        <div class="summary-list">
                            

                            @forelse($employeesByDivision as $item)

                                <div class="summary-list-item">

                                    <span class="summary-list-name"
                                          title="{{ $item->division ?? 'No Division' }}">

                                        {{ $item->division ?? 'No Division' }}

                                    </span>

                                    <span class="badge badge-info">

                                        {{ $item->total }}

                                    </span>

                                </div>

                            @empty

                                <div class="text-muted text-center">

                                    No division data

                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             EMPLOYEE DATA
        ===================================================== --}}

        <div class="card filter-card">

            <div class="card-header">

                <h4>
                    Employee Data
                </h4>

                <div class="card-header-action">

                    <a href="{{ route('employees.create') }}"
                       class="btn btn-primary">

                        + Add Employee

                    </a>

                    <a href="{{ route('employees.template') }}"
                       class="btn btn-success">

                        Download Template

                    </a>

                    <button class="btn btn-info"
                            data-toggle="modal"
                            data-target="#importModal">

                        Import CSV

                    </button>

                </div>

            </div>


            <div class="card-body">


                {{-- =================================================
                     SUCCESS MESSAGE
                ================================================= --}}

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
                     ERROR MESSAGE
                ================================================= --}}

                @if(session('error'))

                    <div class="alert alert-danger alert-dismissible fade show">

                        {{ session('error') }}

                        <button type="button"
                                class="close"
                                data-dismiss="alert">

                            <span>&times;</span>

                        </button>

                    </div>

                @endif


                {{-- =================================================
                     VALIDATION ERROR
                ================================================= --}}

                @if($errors->any())

                    <div class="alert alert-danger">

                        <strong>
                            Please check the following errors:
                        </strong>

                        <ul class="mb-0 mt-2">

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                {{-- =================================================
                    FILTER
                ================================================= --}}

                <form method="GET"
                    id="filterForm"
                    class="row mb-4">


                    {{-- CLIENT FILTER --}}
                    <div class="col-md-3">

                        <label>
                            Client
                        </label>

                        <select name="client_id"
                                class="form-control">

                            <option value="">
                                All Client
                            </option>

                            @foreach($clients as $client)

                                <option value="{{ $client->id }}"
                                    {{ $clientId == $client->id ? 'selected' : '' }}>

                                    {{ $client->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DIVISION FILTER --}}
                    <div class="col-md-3">

                        <label>
                            Division
                        </label>

                        <select name="division"
                                class="form-control">

                            <option value="">
                                All Division
                            </option>

                            @foreach($divisions as $div)

                                <option value="{{ $div }}"
                                    {{ $division == $div ? 'selected' : '' }}>

                                    {{ $div }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- EMPLOYEE STATUS --}}
                    <div class="col-md-3">

                        <label>
                            Employee Status
                        </label>

                        <select name="status"
                                class="form-control">

                            <option value="">
                                All Status
                            </option>

                            <option value="active"
                                {{ $status == 'active' ? 'selected' : '' }}>

                                Active

                            </option>

                            <option value="inactive"
                                {{ $status == 'inactive' ? 'selected' : '' }}>

                                Inactive

                            </option>

                        </select>

                    </div>


                    {{-- CONTRACT STATUS --}}
                    <div class="col-md-3">

                        <label>
                            Contract Status
                        </label>

                        <select name="contract_expiring"
                                class="form-control">

                            <option value="">
                                All Contract
                            </option>

                            <option value="30"
                                {{ $contractExpiringFilter == '30' ? 'selected' : '' }}>

                                Expiring Within 30 Days

                            </option>

                            <option value="expired"
                                {{ $contractExpiringFilter == 'expired' ? 'selected' : '' }}>

                                Expired

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


                        <a href="{{ route('employees.index') }}"
                        class="btn btn-secondary">

                            <i class="fas fa-sync-alt"></i>

                            Reset

                        </a>

                    </div>

                </form>


                {{-- =================================================
                     LOADING OVERLAY
                ================================================= --}}

                <div id="loadingOverlay">

                    <div class="loading-box">

                        <div class="spinner-border text-primary mb-3">
                        </div>

                        <h6>
                            Loading employee data...
                        </h6>

                        <small class="text-muted">
                            Please Wait
                        </small>

                    </div>

                </div>


                {{-- =================================================
                     EMPLOYEE TABLE
                ================================================= --}}

                <div class="table-responsive">

                    <table class="table-striped table employee-table"
                           id="table-1">

                        <thead>

                            <tr>

                                <th>#</th>
                                <th>User ID</th>
                                <th>Client</th>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>NIK</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>Division</th>
                                <th>Placement</th>
                                <th>Join Date</th>
                                <th>Contract Start</th>
                                <th>Contract End</th>
                                <th>Extension</th>
                                <th>Absent Using Distance</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Action</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($employees as $key => $emp)

                                <tr>

                                    <td>
                                        {{ $key + 1 }}
                                    </td>

                                    <td>
                                        {{ $emp->user_id }}
                                    </td>

                                    <td>
                                        {{ $emp->client->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $emp->employee_id }}
                                    </td>

                                    <td>
                                        {{ $emp->full_name }}
                                    </td>

                                    <td>
                                        {{ $emp->nik_ktp }}
                                    </td>

                                    <td>
                                        {{ $emp->phone }}
                                    </td>

                                    <td>
                                        {{ $emp->email }}
                                    </td>

                                    <td>
                                        {{ $emp->position }}
                                    </td>

                                    <td>
                                        {{ $emp->division }}
                                    </td>

                                    <td>
                                        {{ $emp->placement }}
                                    </td>

                                    <td>
                                        {{ $emp->join_date }}
                                    </td>

                                    <td>
                                        {{ $emp->contract_start }}
                                    </td>

                                    <td>

                                        @if($emp->contract_end)

                                            {{ $emp->contract_end }}

                                            @php

                                                $contractDate = \Carbon\Carbon::parse(
                                                    $emp->contract_end
                                                );

                                                $today = \Carbon\Carbon::today();

                                                $daysLeft = $today->diffInDays(
                                                    $contractDate,
                                                    false
                                                );

                                            @endphp


                                            @if($daysLeft < 0)

                                                <br>

                                                <span class="badge badge-danger">

                                                    Expired

                                                </span>

                                            @elseif($daysLeft <= 30)

                                                <br>

                                                <span class="badge badge-warning">

                                                    {{ $daysLeft }} days left

                                                </span>

                                            @endif

                                        @else

                                            -

                                        @endif

                                    </td>

                                    <td>
                                        {{ $emp->contract_extension_count }}
                                    </td>

                                    <td>

                                        @if($emp->absent_using_distance)

                                            <span class="badge badge-success">
                                                TRUE
                                            </span>

                                        @else

                                            <span class="badge badge-secondary">
                                                FALSE
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        <span class="badge
                                            {{ $emp->status == 'active'
                                                ? 'badge-success'
                                                : 'badge-danger' }}">

                                            {{ $emp->status }}

                                        </span>

                                    </td>

                                    <td>
                                        {{ $emp->notes }}
                                    </td>


                                    {{-- ACTION --}}

                                    <td style="white-space: nowrap;">

                                        {{-- EDIT --}}

                                        <a href="{{ route('employees.edit', $emp->id) }}"
                                           class="btn btn-warning btn-sm">

                                            Edit

                                        </a>


                                        {{-- DELETE --}}

                                        <form action="{{ route('employees.destroy', $emp->id) }}"
                                              method="POST"
                                              style="display:inline;">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this data?')">

                                                Delete

                                            </button>

                                        </form>


                                        {{-- RESET FACE --}}

                                        <form action="{{ route('employees.reset-face', $emp->id) }}"
                                              method="POST"
                                              style="display:inline;">

                                            @csrf

                                            <button type="submit"
                                                    class="btn btn-info btn-sm"
                                                    onclick="return confirm('Reset face data for this employee?')">

                                                Reset Face

                                            </button>

                                        </form>


                                        {{-- ATTENDANCE --}}

                                        <a href="{{ route('employees.attendance', $emp->id) }}"
                                           class="btn btn-primary btn-sm">

                                            Attendance

                                        </a>

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


{{-- =============================================================
     IMPORT MODAL
============================================================= --}}

<div class="modal fade"
     id="importModal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog">

        <form action="{{ route('employees.import') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="modal-content">


                <div class="modal-header">

                    <h5 class="modal-title">
                        Import Employee CSV
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>


                <div class="modal-body">

                    <div class="form-group">

                        <label>
                            Employee CSV File
                        </label>

                        <input type="file"
                               name="file"
                               class="form-control"
                               accept=".csv,.txt"
                               required>

                    </div>

                    <small class="text-muted">

                        Make sure the CSV follows the downloaded
                        employee template format.

                    </small>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        Upload

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}">
</script>

<script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}">
</script>

<script src="{{ asset('js/page/modules-datatables.js') }}">
</script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const filterForm = document.getElementById('filterForm');

    const loadingOverlay = document.getElementById('loadingOverlay');

    if (!filterForm) {
        return;
    }

    filterForm.addEventListener('submit', function () {

        loadingOverlay.style.display = 'flex';

    });

});

</script>

@endpush