@extends('layouts.sec')

@section('title', 'Pending Registrations')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}">
    <style>.data-pending-panel.hidden { display: none; }</style>
@endpush

@section('content')
<div class="data-page mt-3">
    <div class="card">
        <div class="card-header text-center">
            <h4>Pending Registrations</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 data-pending-toggle">
                <div>
                    <button type="button" id="showStudents" class="btn btn-primary me-2">Students</button>
                    <button type="button" id="showEmployees" class="btn btn-outline-primary">Employees</button>
                </div>
                <div class="data-tabs">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm">Registered Students</a>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm">Registered Employees</a>
                </div>
            </div>

            <form id="pending-filter-form" method="GET" action="{{ route('pending.index') }}" class="row g-2 mb-3">
                <input type="hidden" name="tab" id="pendingTab" value="{{ request('tab', 'students') }}">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ $search ?? request('search') }}" class="form-control form-control-sm" placeholder="Search pending records…">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-search-filter">Search</button>
                </div>
            </form>

            <div id="pending-students-panel"
                 class="data-pending-panel"
                 data-hydratable-panel
                 data-loading="false"
                 data-form="#pending-filter-form"
                 data-skeleton="#pending-students-skeleton"
                 data-pagination=".data-panel-pagination"
                 data-path-match="/pending"
                 data-enabled-when-visible="true"
                 data-tab-input="#pendingTab">
                @include('pending.partials.students-table')
            </div>

            <div id="pending-employees-panel"
                 class="data-pending-panel hidden"
                 data-hydratable-panel
                 data-loading="false"
                 data-form="#pending-filter-form"
                 data-skeleton="#pending-employees-skeleton"
                 data-pagination=".data-panel-pagination"
                 data-path-match="/pending"
                 data-enabled-when-visible="true"
                 data-tab-input="#pendingTab">
                @include('pending.partials.employees-table')
            </div>
        </div>
    </div>
</div>

<template id="pending-students-skeleton">
    @include('partials.skeleton-table', [
        'columns' => 6,
        'rows' => 8,
        'loadingLabel' => 'Loading pending students…',
        'headers' => ['Profile', 'ID Number', 'Name', 'Course', 'Year', 'Actions'],
        'skeletonFirstCol' => 'avatar',
    ])
</template>

<template id="pending-employees-skeleton">
    @include('partials.skeleton-table', [
        'columns' => 5,
        'rows' => 8,
        'loadingLabel' => 'Loading pending employees…',
        'headers' => ['Profile', 'Name', 'Department', 'Position', 'Actions'],
        'skeletonFirstCol' => 'avatar',
    ])
</template>
@endsection

@section('scripts')
<script>
(function () {
    const studentPanel = document.getElementById('pending-students-panel');
    const employeePanel = document.getElementById('pending-employees-panel');
    const btnStudents = document.getElementById('showStudents');
    const btnEmployees = document.getElementById('showEmployees');
    const tabInput = document.getElementById('pendingTab');

    function showStudents() {
        studentPanel.classList.remove('hidden');
        employeePanel.classList.add('hidden');
        btnStudents.className = 'btn btn-primary me-2';
        btnEmployees.className = 'btn btn-outline-primary';
        tabInput.value = 'students';
    }

    function showEmployees() {
        employeePanel.classList.remove('hidden');
        studentPanel.classList.add('hidden');
        btnEmployees.className = 'btn btn-primary me-2';
        btnStudents.className = 'btn btn-outline-primary';
        tabInput.value = 'employees';
    }

    btnStudents.addEventListener('click', showStudents);
    btnEmployees.addEventListener('click', showEmployees);

    const tab = new URLSearchParams(window.location.search).get('tab') || 'students';
    if (tab === 'employees') showEmployees();
    else showStudents();
})();
</script>
@endsection
