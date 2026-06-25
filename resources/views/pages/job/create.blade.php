@extends('layouts.app')

@section('title', 'Tambah Lowongan')

@push('style')
<link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Add Vacancies</h1>
        </div>

        <div class="card">
            <div class="card-body">

                {{-- SUCCESS --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ERROR --}}
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- VALIDATION ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger">

                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('career.store') }}">
                    @csrf

                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="{{ old('title') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Location <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="location"
                            class="form-control"
                            value="{{ old('location') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="">-- Select Type --</option>

                            <option
                                value="Fulltime"
                                {{ old('type') == 'Fulltime' ? 'selected' : '' }}
                            >
                                Fulltime
                            </option>

                            <option
                                value="Parttime"
                                {{ old('type') == 'Parttime' ? 'selected' : '' }}
                            >
                                Parttime
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>

                        <textarea
                            name="description"
                            id="description"
                            class="summernote"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Requirement <span class="text-danger">*</span></label>

                        <textarea
                            name="requirement"
                            id="requirement"
                            class="summernote"
                        >{{ old('requirement') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Benefit <span class="text-danger">*</span></label>

                        <textarea
                            name="benefit"
                            id="benefit"
                            class="summernote"
                        >{{ old('benefit') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Salary Min <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            name="salary_min"
                            class="form-control"
                            value="{{ old('salary_min') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Salary Max <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            name="salary_max"
                            class="form-control"
                            value="{{ old('salary_max') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Deadline <span class="text-danger">*</span></label>
                        <input
                            type="date"
                            name="deadline"
                            class="form-control"
                            value="{{ old('deadline') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>

                        <select
                            name="is_active"
                            class="form-control"
                            required
                        >
                            <option value="">-- Select Status --</option>

                            <option
                                value="1"
                                {{ old('is_active') == '1' ? 'selected' : '' }}
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                {{ old('is_active') == '0' ? 'selected' : '' }}
                            >
                                Non Active
                            </option>
                        </select>
                    </div>

                    <button class="btn btn-primary">
                        Save
                    </button>

                </form>

            </div>
        </div>

    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('library/summernote/dist/summernote-bs4.js') }}"></script>

<script>
    $('.summernote').summernote({
        height: 200
    });
</script>
@endpush