@extends('layouts.public')

@section('title', 'Career')

@section('content')

<h3 class="mb-4">Lowongan Tersedia</h3>

<div class="row">
@foreach($jobs as $job)
<div class="col-md-4 mb-4">
    <div class="card job-card shadow-sm">
        <div class="card-body">

            <h5>{{ $job->title }}</h5>

            <p>
                <i class="fas fa-map-marker-alt"></i> {{ $job->location }}
            </p>

            <p>
                <i class="fas fa-briefcase"></i> {{ $job->type }}
            </p>

            <p class="text-success font-weight-bold">
                Rp {{ number_format($job->salary_min) }} -
                {{ number_format($job->salary_max) }}
            </p>

            <a href="{{ route('public.show', $job->slug) }}"
                class="btn btn-primary btn-block">
                Lihat Detail
            </a>

        </div>
    </div>
</div>
@endforeach
</div>

@endsection