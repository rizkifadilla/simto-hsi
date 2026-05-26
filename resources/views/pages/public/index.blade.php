@extends('layouts.public')

@section('title', 'Job Vacancy')

@section('content')

<div class="row g-4">

    <!-- ================= LEFT ================= -->
    <div class="col-lg-8">

        <!-- SEARCH -->
        <form method="GET" action="{{ route('public.index') }}" class="mb-3">
            <div class="input-group">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Search for a position or location..."
                >
                <button class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- SORT -->
        <form method="GET" class="mb-3">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <select 
                name="sort" 
                class="form-select" 
                onchange="this.form.submit()"
            >
                <option value="">Latest</option>
                <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>
                    Deadline
                </option>
                <option value="salary" {{ request('sort') == 'salary' ? 'selected' : '' }}>
                    Salary
                </option>
            </select>
        </form>

        <!-- JOB LIST -->
        @forelse($jobs as $job)
            <div class="job-card">
                <div class="d-flex justify-content-between">

                    <div>
                        <h6>{{ $job->title }}</h6>

                        <small class="text-muted">
                            <i class="bi bi-geo-alt"></i> {{ $job->location }} |
                            {{ $job->type }}
                        </small>

                        <div class="mt-2 text-primary fw-bold">
                            Rp {{ number_format($job->salary_min) }}
                            -
                            Rp {{ number_format($job->salary_max) }}
                        </div>

                        <div class="small">
                            Deadline: {{ $job->deadline }}
                        </div>
                    </div>

                    <div class="text-end">
                        @if($job->is_active)
                            <span class="badge-active">Active</span>
                        @else
                            <span class="badge-inactive">Closed</span>
                        @endif

                        <br><br>

                        <a 
                            href="{{ route('public.show', $job->slug) }}"
                            class="btn btn-sm btn-primary"
                            {{ !$job->is_active ? 'disabled' : '' }}
                        >
                            Apply
                        </a>
                    </div>

                </div>
            </div>
        @empty
            <div class="text-center text-muted">
                There are no vacancies
            </div>
        @endforelse

        {{ $jobs->links() }}

    </div>

    <!-- ================= RIGHT (SIDEBAR) ================= -->
    <div class="col-lg-4">

        <form method="GET" action="{{ route('public.index') }}">

            <!-- TYPE -->
            <div class="card mb-3 p-3">
                <h6>Job Type</h6>

                @foreach(['fulltime', 'parttime'] as $type)
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox"
                            name="type[]" 
                            value="{{ $type }}"
                            {{ in_array($type, request('type', [])) ? 'checked' : '' }}
                        >
                        <label class="form-check-label">
                            {{ ucfirst($type) }}
                        </label>
                    </div>
                @endforeach
            </div>

            <!-- STATUS -->
            <div class="card mb-3 p-3">
                <h6>Status</h6>

                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="radio"
                        name="status" 
                        value="active"
                        {{ request('status') == 'active' ? 'checked' : '' }}
                    >
                    <label class="form-check-label">
                        Only Active
                    </label>
                </div>

                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="radio"
                        name="status" 
                        value=""
                        {{ request('status') == null ? 'checked' : '' }}
                    >
                    <label class="form-check-label">
                        All
                    </label>
                </div>
            </div>

            <button class="btn btn-primary w-100">
                Apply Filters
            </button>

        </form>

    </div>

</div>

@endsection