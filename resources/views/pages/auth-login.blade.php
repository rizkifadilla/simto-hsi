@extends('layouts.auth')

@section('title', 'Login')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">
@endpush

@section('main')
    <div class="card card-primary">
        <div class="card-header">
            <h4>Login</h4>
        </div>

        <div class="card-body">
            <form method="POST"
            action="{{ route('login') }}"
            class="needs-validation">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    Email atau password salah
                </div>
            @endif

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email"
                    type="email"
                    class="form-control"
                    name="email"
                    value="{{ old('email') }}"
                    required autofocus>
            </div>

            <div class="form-group">
                <div class="d-block">
                    <label for="password" class="control-label">Password</label>
                    <div class="float-right">
                        <a href="#" class="text-small">
                            Forgot Password?
                        </a>
                    </div>
                </div>

                <input id="password"
                    type="password"
                    class="form-control"
                    name="password"
                    required>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                        name="remember"
                        class="custom-control-input"
                        id="remember-me"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="remember-me">
                        Remember Me
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit"
                        class="btn btn-primary btn-lg btn-block">
                    Login
                </button>
            </div>
        </form>

        </div>
    </div>
    <div class="text-muted mt-5 text-center">
        Don't have an account? <a href="auth-register.html">Create One</a>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
