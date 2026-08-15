@extends('layouts.public')

@section('title', 'Track Application')

@section('content')

<div class="col-lg-8 mx-auto">

    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            {{-- HEADER --}}
            <div class="text-center mb-4">

                <div class="mb-3">
                    <i class="bi bi-clipboard-check"
                       style="font-size: 45px; color:#6777ef;"></i>
                </div>

                <h3 class="mb-2">
                    Track Your Application
                </h3>

                <p class="text-muted mb-0">
                    Enter the email address you used when submitting
                    your application.
                </p>

            </div>


            {{-- SUCCESS --}}
            @if(session('success'))

                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    {{ session('success') }}
                </div>

            @endif


            {{-- ERROR --}}
            @if(session('error'))

                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ session('error') }}
                </div>

            @endif


            {{-- VALIDATION --}}
            @if($errors->any())

                <div class="alert alert-danger">

                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach

                </div>

            @endif


            {{-- EMAIL FORM --}}
            <form method="POST"
                  action="{{ route('public.tracking.send-otp') }}">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Email Address
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control"
                               placeholder="Enter your email"
                               required>

                    </div>

                    <small class="text-muted">
                        Use the same email address you used when applying.
                    </small>

                </div>


                <button type="submit"
                        class="btn btn-primary w-100">

                    <i class="bi bi-send"></i>
                    Send Verification Code

                </button>

            </form>

        </div>

    </div>


    {{-- INFORMATION --}}
    <div class="card border-0 shadow-sm mt-3">

        <div class="card-body">

            <h6>
                <i class="bi bi-info-circle"></i>
                How it works
            </h6>

            <div class="row mt-3">

                <div class="col-md-4 text-center mb-3">

                    <div class="mb-2">
                        <i class="bi bi-envelope"
                           style="font-size:30px;color:#6777ef;"></i>
                    </div>

                    <strong>1. Enter Email</strong>

                    <p class="small text-muted mb-0">
                        Enter the email used when applying.
                    </p>

                </div>


                <div class="col-md-4 text-center mb-3">

                    <div class="mb-2">
                        <i class="bi bi-shield-lock"
                           style="font-size:30px;color:#28a745;"></i>
                    </div>

                    <strong>2. Verify OTP</strong>

                    <p class="small text-muted mb-0">
                        A verification code will be sent to your email.
                    </p>

                </div>


                <div class="col-md-4 text-center mb-3">

                    <div class="mb-2">
                        <i class="bi bi-clipboard-check"
                           style="font-size:30px;color:#17a2b8;"></i>
                    </div>

                    <strong>3. View Status</strong>

                    <p class="small text-muted mb-0">
                        View your application status securely.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection