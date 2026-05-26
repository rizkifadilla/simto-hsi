@extends('layouts.app')

@section('title', 'Edit Lowongan')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Edit Vacancies</h1>
        </div>

        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ route('career.update', $job->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Title</label>
                        <input 
                            type="text" 
                            name="title" 
                            value="{{ $job->title }}" 
                            class="form-control" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input 
                            type="text" 
                            name="location" 
                            value="{{ $job->location }}" 
                            class="form-control"
                        >
                    </div>

                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control selectric">
                            <option value="Fulltime" {{ $job->type == 'Fulltime' ? 'selected' : '' }}>
                                Fulltime
                            </option>
                            <option value="Parttime" {{ $job->type == 'Parttime' ? 'selected' : '' }}>
                                Parttime
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="summernote">{{ $job->description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Requirement</label>
                        <textarea name="requirement" class="summernote">{{ $job->requirement }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Benefit</label>
                        <textarea name="benefit" class="summernote">{{ $job->benefit }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Salary Min</label>
                        <input 
                            type="number" 
                            name="salary_min" 
                            value="{{ $job->salary_min }}" 
                            class="form-control"
                        >
                    </div>

                    <div class="form-group">
                        <label>Salary Max</label>
                        <input 
                            type="number" 
                            name="salary_max" 
                            value="{{ $job->salary_max }}" 
                            class="form-control"
                        >
                    </div>

                    <div class="form-group">
                        <label>Deadline</label>
                        <input 
                            type="date" 
                            name="deadline"
                            value="{{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('Y-m-d') : '' }}"
                            class="form-control"
                        >
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ $job->is_active ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ !$job->is_active ? 'selected' : '' }}>
                                Non Active
                            </option>
                        </select>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary">
                            Update
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </section>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('library/summernote/dist/summernote-bs4.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <script>
        $('.summernote').summernote({
            height: 200
        });

        $('.selectric').selectric();
    </script>
@endpush