@extends('layouts.public')

@section('title', $job->title)

@section('content')

<div class="mb-3">
    <a href="{{ route('public.index') }}" class="btn btn-outline-primary btn-sm">
        ← Back to Vacancies
    </a>
</div>

<div class="row g-4">

    <!-- ================= LEFT ================= -->
    <div class="col-lg-8">

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <h3 class="mb-2">{{ $job->title }}</h3>

                <div class="text-muted mb-3 small">
                    {{ $job->location }} | {{ ucfirst($job->type) }} |
                    Deadline: {{ $job->deadline ?? '-' }}
                </div>

                @if($job->salary_min && $job->salary_max)
                    <div class="mb-3">
                        <span class="badge bg-primary">
                            Rp {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                        </span>
                    </div>
                @endif

                <hr>

                <h5>Description</h5>
                {!! $job->description !!}

                @if($job->requirement)
                    <h5 class="mt-3">Requirement</h5>
                    {!! $job->requirement !!}
                @endif

                @if($job->benefit)
                    <h5 class="mt-3">Benefit</h5>
                    {!! $job->benefit !!}
                @endif

            </div>
        </div>

    </div>

    <!-- ================= RIGHT ================= -->
    <div class="col-lg-4">

        <div class="card shadow-sm border-0 sticky-top" style="top:20px;">
            <div class="card-body">

                <h5 class="mb-3">Apply Now</h5>

                @if(session('success'))
                    <div class="alert alert-success small">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger small">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger small">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('public.apply') }}" enctype="multipart/form-data" id="applyForm">
                    @csrf

                    <input type="hidden" name="job_id" value="{{ $job->id }}">
                    <input type="hidden" name="g-recaptcha-response" id="recaptcha">

                    <div class="mb-2">
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="Name" required>
                    </div>

                    <div class="mb-2">
                        <input type="email" name="email" class="form-control form-control-sm"
                               placeholder="Email" required>
                    </div>

                    <div class="mb-2">
                        <input type="text" name="phone" class="form-control form-control-sm"
                               placeholder="Phone Number">
                    </div>

                    <div class="mb-2">
                        <textarea name="address" class="form-control form-control-sm"
                                  placeholder="Address"></textarea>
                    </div>

                    <div class="mb-2">
                        <textarea name="cover_letter" class="form-control form-control-sm"
                                placeholder="Cover Letter"></textarea>
                    </div>

                    <div class="mb-3">
                        <input type="file" name="cv"
                            class="form-control form-control-sm" required>
                    </div>

                    @if(!$job->is_active)
                        <button class="btn btn-secondary w-100" disabled>
                            Vacancies Closed
                        </button>
                    @else
                        <button class="btn btn-success w-100">
                            Submit Application
                        </button>
                    @endif

                </form>

            </div>
        </div>

    </div>

</div>

<!-- RECAPTCHA V3 -->
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

<script>
document.getElementById('applyForm').addEventListener('submit', function(e) {
    e.preventDefault();

    grecaptcha.ready(function() {
        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'submit'}).then(function(token) {
            document.getElementById('recaptcha').value = token;
            document.getElementById('applyForm').submit();
        });
    });
});
</script>

@endsection