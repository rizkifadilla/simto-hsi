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

                <form method="POST" action="{{ route('career.store') }}">
                    @csrf

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option>Fulltime</option>
                            <option>Parttime</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="summernote"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Requirement</label>
                        <textarea name="requirement" class="summernote"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Benefit</label>
                        <textarea name="benefit" class="summernote"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Salary Min</label>
                        <input type="number" name="salary_min" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Salary Max</label>
                        <input type="number" name="salary_max" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="date" name="deadline" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Non Active</option>
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