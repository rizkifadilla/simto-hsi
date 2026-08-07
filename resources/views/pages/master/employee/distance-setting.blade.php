@extends('layouts.app')

@section('title', 'Distance Setting')

@section('main')
<div class="main-content">
<section class="section">

    <div class="section-header">
        <h1>Distance Setting</h1>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}

                    <button type="button"
                            class="close"
                            data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            {{-- FILTER --}}
            <form method="GET"
                class="row mb-4"
                id="filterForm">

                <div class="col-md-5">
                    <label>Client</label>

                    <select name="client_id"
                            class="form-control">

                        <option value="">
                            All Client
                        </option>

                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ $clientId == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-5">
                    <label>Division</label>

                    <select name="division"
                            class="form-control">

                        <option value="">
                            All Division
                        </option>

                        @foreach($divisions as $div)
                            <option value="{{ $div }}"
                                {{ $division == $div ? 'selected' : '' }}>
                                {{ $div }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>

            </form>

            <div id="loadingOverlay" style="display:none;">
                <div class="loading-box">
                    <div class="spinner-border text-primary mb-3"></div>

                    <h6>Please Wait</h6>

                    <small class="text-muted">
                        Processing...
                    </small>
                </div>
            </div>

            <form method="POST"
                id="saveForm"
                action="{{ route('employees.distance-setting.update') }}">

                @csrf

                <input type="hidden"
                       name="client_id"
                       value="{{ $clientId }}">

                <input type="hidden"
                       name="division"
                       value="{{ $division }}">

                <div class="mb-3 float-right">

                    <button class="btn btn-success">
                        Save Setting
                    </button>

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>

                            <tr>

                                <th width="50"
                                    class="text-center">

                                    <input type="checkbox"
                                           id="checkAll">

                                </th>

                                <th>Employee</th>
                                <th>Client</th>
                                <th>Division</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($employees as $employee)

                                <tr>

                                    <td class="text-center">

                                        <input type="checkbox"
                                               class="employee-checkbox"
                                               name="employee_ids[]"
                                               value="{{ $employee->id }}"
                                               {{ $employee->absent_using_distance ? 'checked' : '' }}>

                                    </td>

                                    <td>
                                        <strong>
                                            {{ $employee->full_name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $employee->client->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $employee->division }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4"
                                        class="text-center">
                                        No Data
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </form>

        </div>
    </div>

</section>
</div>
@endsection
@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.employee-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', function () {

            checkboxes.forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });

        });
    }

    const showLoading = function () {
        document.getElementById('loadingOverlay').style.display = 'flex';
    };

    const filterForm = document.getElementById('filterForm');

    if (filterForm) {
        filterForm.addEventListener('submit', showLoading);
    }

    const saveForm = document.getElementById('saveForm');

    if (saveForm) {
        saveForm.addEventListener('submit', showLoading);
    }

});
</script>
@endpush
@push('style')
<style>

#loadingOverlay {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,.7);
    z-index: 99999;

    display: flex;
    align-items: center;
    justify-content: center;

    backdrop-filter: blur(2px);
}

.loading-box {
    background: white;
    padding: 30px 40px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,.1);
    text-align: center;
}

</style>
@endpush