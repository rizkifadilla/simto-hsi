@extends('layouts.app')

@section('title', 'Pelamar')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">

            <div class="section-header">
                <h1>Pelamar - {{ $job->title }}</h1>
            </div>

            <div class="card">
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>CV</th>
                                    <th>Follow Up</th>
                                    <th>Notes HR</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $i => $app)
                                    <tr>
                                        <form action="{{ route('career.application.update', $app->id) }}" method="POST">
                                            @csrf

                                            <td>{{ $i + 1 }}</td>

                                            <td>{{ $app->applicant->name }}</td>
                                            <td>{{ $app->applicant->email }}</td>
                                            <td>{{ $app->applicant->phone }}</td>

                                            <td>
                                                <a href="{{ asset('storage/' . $app->applicant->cv_file) }}"
                                                    target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    CV
                                                </a>
                                            </td>

                                            {{-- FOLLOW UP --}}
                                            <td class="text-center">
                                                <input type="checkbox"
                                                    name="follow_up"
                                                    value="1"
                                                    {{ $app->followed_up_at ? 'checked' : '' }}>
                                            </td>

                                            {{-- NOTES --}}
                                            <td style="min-width:200px;">
                                                <textarea name="notes"
                                                    class="form-control form-control-sm"
                                                    rows="2"
                                                    placeholder="Tulis catatan HR...">{{ $app->notes }}</textarea>
                                            </td>

                                            {{-- ACTION --}}
                                            <td>
                                                <button class="btn btn-success btn-sm">
                                                    Save
                                                </button>
                                            </td>

                                        </form>
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
        $(document).ready(function () {
            $('#table-1').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10
            });
        });
    </script>
@endpush