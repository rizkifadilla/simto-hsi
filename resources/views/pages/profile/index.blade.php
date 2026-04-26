@extends('layouts.app')

@section('title', 'Profile')

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Profile</h1>
        </div>

        <div class="row">

            {{-- INFO USER --}}
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">

                        <img 
                            src="{{ asset('img/avatar/avatar-1.png') }}"
                            class="rounded-circle mb-3"
                            width="100"
                        >

                        <h5>{{ auth()->user()->name }}</h5>
                        <p class="text-muted">{{ auth()->user()->email }}</p>

                    </div>
                </div>
            </div>

            {{-- FORM PASSWORD --}}
            <div class="col-md-8">
                <div class="card">

                    <div class="card-header">
                        <h4>Ganti Password</h4>
                    </div>

                    <div class="card-body">

                        {{-- SUCCESS --}}
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf

                            {{-- PASSWORD LAMA --}}
                            <div class="form-group">
                                <label>Password Lama</label>

                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        name="current_password" 
                                        id="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                    >

                                    <div class="input-group-append">
                                        <span 
                                            class="input-group-text" 
                                            onclick="togglePassword('current_password')" 
                                            style="cursor:pointer;"
                                        >
                                            👁
                                        </span>
                                    </div>
                                </div>

                                @error('current_password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- PASSWORD BARU --}}
                            <div class="form-group">
                                <label>Password Baru</label>

                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                    >

                                    <div class="input-group-append">
                                        <span 
                                            class="input-group-text" 
                                            onclick="togglePassword('password')" 
                                            style="cursor:pointer;"
                                        >
                                            👁
                                        </span>
                                    </div>
                                </div>

                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- KONFIRMASI --}}
                            <div class="form-group">
                                <label>Konfirmasi Password</label>

                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        id="password_confirmation"
                                        class="form-control"
                                    >

                                    <div class="input-group-append">
                                        <span 
                                            class="input-group-text" 
                                            onclick="togglePassword('password_confirmation')" 
                                            style="cursor:pointer;"
                                        >
                                            👁
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary">
                                Update Password
                            </button>

                        </form>

                    </div>
                </div>
            </div>

        </div>

    </section>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush