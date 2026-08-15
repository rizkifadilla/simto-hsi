@extends('layouts.app')

@section('title', 'Applicants')

@push('style')

<link rel="stylesheet"
      href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">

<style>

    .status-select {
        min-width: 130px;
    }

    .interview-fields {
        display: none;
        min-width: 250px;
    }

    .interview-fields.show {
        display: block;
    }

    .applicant-name {
        font-weight: 600;
    }

    .applicant-email {
        font-size: 12px;
        color: #6c757d;
    }

</style>

@endpush


@section('main')

<div class="main-content">

<section class="section">

    {{-- HEADER --}}

    <div class="section-header">

        <h1>
            Applicants - {{ $job->title }}
        </h1>

    </div>


    <div class="card">

        <div class="card-body">

            {{-- SUCCESS --}}

            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show">

                    {{ session('success') }}

                    <button type="button"
                            class="close"
                            data-dismiss="alert">

                        &times;

                    </button>

                </div>

            @endif


            {{-- ERROR --}}

            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show">

                    {{ session('error') }}

                    <button type="button"
                            class="close"
                            data-dismiss="alert">

                        &times;

                    </button>

                </div>

            @endif


            <div class="table-responsive">

                <table class="table table-striped"
                       id="table-1">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Applicant</th>

                            <th>Phone</th>

                            <th>CV</th>

                            <th>Status</th>

                            <th>Interview</th>

                            <th>Follow Up</th>

                            <th>Notes HR</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($applications as $i => $app)

                            <tr>

                                <form
                                    action="{{ route('career.application.update', $app->id) }}"
                                    method="POST"
                                    class="application-form"
                                >

                                    @csrf


                                    {{-- NUMBER --}}

                                    <td>
                                        {{ $i + 1 }}
                                    </td>


                                    {{-- APPLICANT --}}

                                    <td>

                                        <div class="applicant-name">

                                            {{ $app->applicant->name }}

                                        </div>

                                        <div class="applicant-email">

                                            {{ $app->applicant->email }}

                                        </div>

                                    </td>


                                    {{-- PHONE --}}

                                    <td>

                                        {{ $app->applicant->phone ?? '-' }}

                                    </td>


                                    {{-- CV --}}

                                    <td>

                                        @if($app->applicant->cv_file)

                                            <a
                                                href="{{ asset('storage/' . $app->applicant->cv_file) }}"
                                                target="_blank"
                                                class="btn btn-primary btn-sm"
                                            >

                                                <i class="fas fa-file-pdf"></i>

                                                CV

                                            </a>

                                        @else

                                            -

                                        @endif

                                    </td>


                                    {{-- STATUS --}}

                                    <td>

                                        <select
                                            name="status"
                                            class="form-control form-control-sm status-select status-select-{{ $app->id }}"
                                        >

                                            <option
                                                value="submitted"
                                                {{ $app->status === 'submitted' ? 'selected' : '' }}
                                            >
                                                Submitted
                                            </option>

                                            <option
                                                value="screening"
                                                {{ $app->status === 'screening' ? 'selected' : '' }}
                                            >
                                                Screening
                                            </option>

                                            <option
                                                value="interview"
                                                {{ $app->status === 'interview' ? 'selected' : '' }}
                                            >
                                                Interview
                                            </option>

                                            <option
                                                value="accepted"
                                                {{ $app->status === 'accepted' ? 'selected' : '' }}
                                            >
                                                Accepted
                                            </option>

                                            <option
                                                value="rejected"
                                                {{ $app->status === 'rejected' ? 'selected' : '' }}
                                            >
                                                Rejected
                                            </option>

                                        </select>

                                    </td>


                                    {{-- INTERVIEW --}}

                                    <td>

                                        <div
                                            class="interview-fields interview-fields-{{ $app->id }}
                                            {{ $app->status === 'interview' ? 'show' : '' }}"
                                        >

                                            <input
                                                type="date"
                                                name="interview_date"
                                                class="form-control form-control-sm mb-1"
                                                value="{{ $app->interview_date?->format('Y-m-d') }}"
                                            >


                                            <input
                                                type="time"
                                                name="interview_time"
                                                class="form-control form-control-sm mb-1"
                                                value="{{ $app->interview_time
                                                    ? \Carbon\Carbon::parse($app->interview_time)->format('H:i')
                                                    : '' }}"
                                            >


                                            <input
                                                type="text"
                                                name="interview_location"
                                                class="form-control form-control-sm"
                                                placeholder="Interview location"
                                                value="{{ $app->interview_location }}"
                                            >

                                        </div>

                                    </td>


                                    {{-- FOLLOW UP --}}

                                    <td class="text-center">

                                        <input
                                            type="checkbox"
                                            name="follow_up"
                                            value="1"
                                            {{ $app->followed_up_at ? 'checked' : '' }}
                                        >

                                    </td>


                                    {{-- NOTES --}}

                                    <td style="min-width:220px;">

                                        <textarea
                                            name="notes"
                                            class="form-control form-control-sm"
                                            rows="2"
                                            placeholder="HR notes..."
                                        >{{ $app->notes }}</textarea>

                                    </td>


                                    {{-- ACTION --}}

                                    <td>

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm"
                                        >

                                            <i class="fas fa-save"></i>

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

        order: [[0, 'asc']],

        pageLength: 10

    });


    /*
    |--------------------------------------------------------------------------
    | SHOW / HIDE INTERVIEW FIELD
    |--------------------------------------------------------------------------
    */

    $('.status-select').each(function () {

        toggleInterviewFields($(this));

    });


    $('.status-select').on('change', function () {

        toggleInterviewFields($(this));

    });


    function toggleInterviewFields(select) {

        const status = select.val();

        const id = select.attr('class')
            .match(/status-select-(\d+)/)[1];

        const fields = $('.interview-fields-' + id);

        if (status === 'interview') {

            fields.addClass('show');

        } else {

            fields.removeClass('show');

        }

    }

});

</script>

@endpush