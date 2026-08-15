@extends('layouts.public')

@section('title', 'Verify Application')

@section('content')

<div class="col-lg-6 mx-auto">

    <div class="card shadow-sm border-0">

        <div class="card-body p-4 text-center">

            <div class="mb-3">

                <i class="bi bi-shield-lock"
                   style="font-size:50px;color:#6777ef;"></i>

            </div>

            <h3>
                Verify Your Email
            </h3>

            <p class="text-muted">

                We have sent a verification code to:

                <br>

                <strong>
                    {{ session('tracking_email') }}
                </strong>

            </p>


            @if(session('error'))

                <div class="alert alert-danger text-left">
                    {{ session('error') }}
                </div>

            @endif


            @if(session('success'))

                <div class="alert alert-success text-left">
                    {{ session('success') }}
                </div>

            @endif


            <form method="POST"
                  action="{{ route('public.tracking.verify-otp') }}">

                @csrf

                <div class="mb-3">

                    <input type="text"
                           name="otp"
                           class="form-control text-center"
                           placeholder="Enter 6-digit OTP"
                           maxlength="6"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           style="font-size:24px;letter-spacing:8px;"
                           required>

                </div>


                <button type="submit"
                        class="btn btn-primary w-100">

                    <i class="bi bi-check-circle"></i>

                    Verify OTP

                </button>

            </form>


            <div class="mt-3">

                <a href="{{ route('public.tracking') }}"
                   class="text-muted">

                    <i class="bi bi-arrow-left"></i>

                    Change Email

                </a>

            </div>

        </div>

    </div>

</div>

@endsection