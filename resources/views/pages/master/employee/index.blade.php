@extends('layouts.app')

@section('title', 'DataTables')

@push('style')
    <!-- CSS Libraries -->
    {{-- <link rel="stylesheet"
        href="assets/modules/datatables/datatables.min.css">
    <link rel="stylesheet"
        href="assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css"> --}}

    <link rel="stylesheet"
        href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Master Employee</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">master</a></div>
                    <div class="breadcrumb-item">Employee</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Employee</h2>
                <p class="section-lead">
                    Manage your employees here
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Employee data</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('employees.create') }}" class="btn btn-primary mb-3">
                                        + Add Employee
                                    </a>
                                </div>
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
                                    <table class="table-striped table"
                                        id="table-1">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User ID</th>
                                                <th>Client</th>
                                                <th>NIK</th>
                                                <th>Nama</th>
                                                <th>KTP</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Jabatan</th>
                                                <th>Divisi</th>
                                                <th>Penempatan</th>
                                                <th>Join Date</th>
                                                <th>Kontrak Mulai</th>
                                                <th>Kontrak Akhir</th>
                                                <th>Perpanjangan</th>
                                                <th>absent_using_distance</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employees as $key => $emp)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $emp->user_id }}</td>
                                                    <td>{{ $emp->client->name ?? '-' }}</td>
                                                    <td>{{ $emp->employee_id }}</td>
                                                    <td>{{ $emp->full_name }}</td>
                                                    <td>{{ $emp->nik_ktp }}</td>
                                                    <td>{{ $emp->phone }}</td>
                                                    <td>{{ $emp->email }}</td>
                                                    <td>{{ $emp->position }}</td>
                                                    <td>{{ $emp->division }}</td>
                                                    <td>{{ $emp->placement }}</td>
                                                    <td>{{ $emp->join_date }}</td>
                                                    <td>{{ $emp->contract_start }}</td>
                                                    <td>{{ $emp->contract_end }}</td>
                                                    <td>{{ $emp->contract_extension_count }}</td>
                                                    <td>
                                                        @if($emp->absent_using_distance)
                                                            <span class="badge badge-success">TRUE</span>
                                                        @else
                                                            <span class="badge badge-secondary">FALSE</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $emp->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $emp->status }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $emp->notes }}</td>
                                                    <td style="white-space: nowrap;">
                                                        <a href="{{ route('employees.edit', $emp->id) }}"
                                                        class="btn btn-warning btn-sm">Edit</a>

                                                        <form action="{{ route('employees.destroy', $emp->id) }}"
                                                            method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('are you sure you want to delete this data?')">
                                                                Delete
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('employees.reset-face', $emp->id) }}"
                                                            method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            <button class="btn btn-info btn-sm"
                                                                onclick="return confirm('Reset face data for this employee?')">
                                                                Reset Face
                                                            </button>
                                                        </form>

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
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    {{-- <script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script> --}}
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    {{-- <script src="{{ asset() }}"></script> --}}
    {{-- <script src="{{ asset() }}"></script> --}}
    <script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>
@endpush
