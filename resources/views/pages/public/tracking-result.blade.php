@extends('layouts.public')

@section('title', 'Application Status')

@section('content')

<div class="col-lg-10 mx-auto">

    {{-- HEADER --}}
    <div class="mb-4">

        <h3 class="mb-1">
            Application Tracking
        </h3>

        <p class="text-muted mb-0">
            Track the status of your job applications.
        </p>

    </div>


    {{-- APPLICANT --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <small class="text-muted">
                        Applicant
                    </small>

                    <h5 class="mb-1">
                        {{ $applicant->name }}
                    </h5>

                    <small class="text-muted">
                        {{ $applicant->email }}
                    </small>

                </div>


                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <a href="{{ route('public.tracking') }}"
                       class="btn btn-outline-secondary btn-sm">

                        <i class="bi bi-arrow-left"></i>

                        Track Another Email

                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- APPLICATION LIST --}}
    @forelse($applications as $application)

        @php

            $status = strtolower($application->status ?? 'submitted');

            $statusClass = match($status) {

                'submitted' => 'bg-secondary',
                'screening' => 'bg-info',
                'review' => 'bg-info',
                'interview' => 'bg-primary',
                'accepted' => 'bg-success',
                'hired' => 'bg-success',
                'rejected' => 'bg-danger',

                default => 'bg-secondary'

            };

        @endphp


        <div class="card shadow-sm border-0 mb-3">

            <div class="card-body">

                <div class="row">

                    {{-- JOB --}}
                    <div class="col-md-8">

                        <h5 class="mb-2">

                            {{ $application->job->title ?? '-' }}

                        </h5>


                        <div class="text-muted small mb-2">

                            <span class="mr-3">

                                <i class="bi bi-geo-alt"></i>

                                {{ $application->job->location ?? '-' }}

                            </span>


                            <span>

                                <i class="bi bi-briefcase"></i>

                                {{ ucfirst($application->job->type ?? '-') }}

                            </span>

                        </div>


                        <div class="small text-muted">

                            Applied:

                            {{ optional($application->created_at)->format('d M Y') }}

                        </div>

                    </div>


                    {{-- STATUS --}}
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        <small class="text-muted d-block mb-2">
                            Application Status
                        </small>

                        <span class="badge {{ $statusClass }}"
                              style="font-size:13px;padding:8px 12px;">

                            {{ ucfirst($application->status ?? 'Submitted') }}

                        </span>

                    </div>

                </div>


                {{-- STATUS TIMELINE --}}
                <hr>


                <div class="row text-center">

                    {{-- SUBMITTED --}}
                    <div class="col">

                        <div>

                            <i class="bi bi-check-circle-fill text-success"
                               style="font-size:24px;"></i>

                        </div>

                        <small>
                            Submitted
                        </small>

                    </div>


                    {{-- SCREENING --}}
                    <div class="col">

                        <div>

                            <i class="bi bi-search
                            {{ in_array($status, ['screening','review','interview','accepted','hired']) ? 'text-success' : 'text-muted' }}"
                               style="font-size:24px;"></i>

                        </div>

                        <small>
                            Screening
                        </small>

                    </div>


                    {{-- INTERVIEW --}}
                    <div class="col">

                        <div>

                            <i class="bi bi-people
                            {{ in_array($status, ['interview','accepted','hired']) ? 'text-success' : 'text-muted' }}"
                               style="font-size:24px;"></i>

                        </div>

                        <small>
                            Interview
                        </small>

                    </div>


                    {{-- FINAL --}}
                    <div class="col">

                        <div>

                            <i class="bi bi-award
                            {{ in_array($status, ['accepted','hired']) ? 'text-success' : 'text-muted' }}"
                               style="font-size:24px;"></i>

                        </div>

                        <small>
                            Final Result
                        </small>

                    </div>

                </div>


                {{-- REJECTED --}}
                @if($status === 'rejected')

                    <div class="alert alert-danger mt-3 mb-0">

                        <i class="bi bi-exclamation-circle"></i>

                        Unfortunately, your application was not selected
                        for this position.

                    </div>

                @endif


                {{-- INTERVIEW --}}
                @if($status === 'interview')

                    <div class="alert alert-info mt-3 mb-0">

                        <i class="bi bi-info-circle"></i>

                        Your application has progressed to the interview
                        stage. Please check your email for further
                        information.

                    </div>

                @endif


                {{-- ACCEPTED --}}
                @if(in_array($status, ['accepted', 'hired']))

                    <div class="alert alert-success mt-3 mb-0">

                        <i class="bi bi-check-circle"></i>

                        Congratulations! Your application has been
                        successfully selected.

                    </div>

                @endif

            </div>

        </div>

    @empty

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <i class="bi bi-inbox"
                   style="font-size:50px;color:#aaa;"></i>

                <h5 class="mt-3">
                    No Applications Found
                </h5>

                <p class="text-muted">
                    We could not find any applications associated
                    with this email address.
                </p>

                <a href="{{ route('public.index') }}"
                   class="btn btn-primary">

                    View Job Vacancies

                </a>

            </div>

        </div>

    @endforelse


    {{-- LOGOUT / CLEAR TRACKING --}}
    <div class="text-center mt-4 mb-4">

        <form method="POST"
              action="{{ route('public.tracking.logout') }}">

            @csrf

            <button class="btn btn-link text-muted">

                <i class="bi bi-box-arrow-right"></i>

                Exit Tracking

            </button>

        </form>

    </div>

</div>

@endsection