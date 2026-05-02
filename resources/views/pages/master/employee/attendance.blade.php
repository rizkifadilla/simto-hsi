@extends('layouts.app')

@section('title', 'Attendance Employee')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Attendance - {{ $employee->full_name }}</h1>
        </div>

        <div class="card">
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <button class="btn btn-primary mb-3 float-right" data-toggle="modal" data-target="#manualAttendanceModal">
                    + Absen Manual
                </button>

                <div class="table-responsive">
                    <table class="table table-striped" id="table-1">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working</th>
                                <th>Task</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $row)
                                <tr>
                                    <td>{{ $row->date }}</td>

                                    <td>{{ $row->check_in ?? '-' }}</td>
                                    <td>{{ $row->check_out ?? '-' }}</td>

                                    <td>
                                        @if($row->working_minutes)
                                            {{ floor($row->working_minutes / 60) }}h 
                                            {{ $row->working_minutes % 60 }}m
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->task == 'sakit')
                                            <span class="badge badge-warning">Sakit</span>
                                        @elseif($row->task == 'izin')
                                            <span class="badge badge-info">Izin</span>
                                        @else
                                            {{ $row->task }}
                                        @endif
                                    </td>

                                    <td>
                                        <button 
                                            class="btn btn-warning btn-sm editBtn"
                                            data-id="{{ $row->id }}"
                                            data-checkin="{{ $row->check_in }}"
                                            data-checkout="{{ $row->check_out }}"
                                        >
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </section>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editForm">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Attendance</h5>
                    <button 
                        type="button" 
                        class="close" 
                        data-dismiss="modal"
                    >
                        &times;
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Check In</label>
                        <input 
                            type="time" 
                            name="check_in" 
                            id="check_in" 
                            class="form-control"
                        >
                    </div>

                    <div class="form-group">
                        <label>Check Out</label>
                        <input 
                            type="time" 
                            name="check_out" 
                            id="check_out" 
                            class="form-control"
                        >
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- MODAL ABSEN MANUAL -->
 <div class="modal fade" id="manualAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('attendance.manual') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Absen Manual</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <select name="type" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

    <script>
        $('#table-1').DataTable();

        $('.editBtn').on('click', function () {
            let id = $(this).data('id');
            let checkin = $(this).data('checkin');
            let checkout = $(this).data('checkout');

            $('#check_in').val(checkin);
            $('#check_out').val(checkout);

            $('#editForm').attr('action', '/attendance/' + id + '/update-time');

            $('#editModal').modal('show');
        });
    </script>
@endpush