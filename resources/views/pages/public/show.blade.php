@extends('layouts.public')

@section('title', 'Detail Lowongan')

@section('content')

<div class="mb-3">
    <a href="{{ route('public.index') }}" class="btn btn-outline-primary">
        ← Kembali ke Lowongan
    </a>
</div>
<div class="row">

<div class="col-md-8">

    <div class="card shadow-sm">
        <div class="card-body">

            <h3>{{ $job->title }}</h3>

            <p>
                <i class="fas fa-map-marker-alt"></i> {{ $job->location }} |
                <i class="fas fa-briefcase"></i> {{ $job->type }}
            </p>

            <hr>

            <h5>Deskripsi</h5>
            {!! $job->description !!}

            <h5>Requirement</h5>
            {!! $job->requirement !!}

            <h5>Benefit</h5>
            {!! $job->benefit !!}

        </div>
    </div>

</div>

<div class="col-md-4">

    <div class="card shadow-sm">
        <div class="card-body">

            <h5>Apply Sekarang</h5>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('public.apply') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="job_id" value="{{ $job->id }}">

                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Nama" required>
                </div>

                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <input type="text" name="phone" class="form-control" placeholder="No HP">
                </div>

                <div class="form-group">
                    <textarea name="address" class="form-control" placeholder="Alamat"></textarea>
                </div>

                <div class="form-group">
                    <textarea name="cover_letter" class="form-control" placeholder="Cover Letter"></textarea>
                </div>

                <div class="form-group">
                    <input type="file" name="cv" class="form-control" required>
                </div>

                <button class="btn btn-success btn-block">
                    Kirim Lamaran
                </button>

            </form>

        </div>
    </div>

</div>

</div>

@endsection