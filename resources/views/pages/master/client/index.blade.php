@extends('layouts.app')

@section('title', 'Client')

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
                <h1>Master Client</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">master</a></div>
                    <div class="breadcrumb-item">Client</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Client</h2>
                <p class="section-lead">
                    Manage your Clients here
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Client data</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('clients.create') }}" class="btn btn-primary mb-3">
                                        + Add Client
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
                                                <th>Name</th>
                                                <th>Address</th>
                                                <th>Contact Person</th>
                                                <th>Phone</th>
                                                <th>Latitude</th>
                                                <th>Longitude</th>
                                                <th>check in time</th>
                                                <th>check out time</th>
                                                <th>radius</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clients as $key => $c)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $c->name }}</td>
                                                    <td>{{ $c->address }}</td>
                                                    <td>{{ $c->contact_person }}</td>
                                                    <td>{{ $c->phone }}</td>
                                                    <td>{{ $c->latitude }}</td>
                                                    <td>{{ $c->longitude }}</td>
                                                    <td>{{ $c->check_in_time }}</td>
                                                    <td>{{ $c->check_out_time }}</td>
                                                    <td>{{ $c->attendance_radius }}</td>
                                                    <td style="white-space: nowrap;">
                                                        <a href="{{ route('clients.edit', $c->id) }}"
                                                        class="btn btn-warning btn-sm">Edit</a>

                                                        <form action="{{ route('clients.destroy', $c->id) }}"
                                                            method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('are you sure you want to delete this data?')">
                                                                Delete
                                                            </button>
                                                        </form>
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
