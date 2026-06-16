@extends('layouts.app')

@section('title', 'Lowongan')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>job vacancy</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Vacancies Data</h4>
                <div class="card-header-action">
                    <a href="{{ route('career.create') }}" class="btn btn-primary">+ Add</a>
                </div>
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped" id="table-1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Salary</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $i => $job)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->location }}</td>
                                    <td>{{ $job->type }}</td>
                                    <td>
                                        {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                                    </td>
                                    <td>
                                        @if($job->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Non Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('career.applicants', $job->id) }}" 
                                        class="btn btn-info btn-sm">
                                            Applicant
                                        </a>
                                        <a href="{{ route('career.edit', $job->id) }}" 
                                        class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form action="{{ route('career.destroy', $job->id) }}" 
                                            method="POST" 
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Delete?')">
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

    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $('#table-1').DataTable();
    </script>
@endpush