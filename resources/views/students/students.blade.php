@extends('layouts.sec')

@section('title', 'Students')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}">
@endpush

@section('content')
<div class="data-page mt-3">
    <div class="card">
        <div class="card-header text-center">
            <h4>Registered Students</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form id="students-filter-form" action="{{ route('students.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search name, ID, course…" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="program_id" class="form-select form-select-sm">
                        <option value="">All Courses</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->program_code }}"
                                {{ request('program_id') == $program->program_code ? 'selected' : '' }}>
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All Years</option>
                        @foreach(['1st Year','2nd Year','3rd Year','4th Year','5th Year','6th Year'] as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-search-filter">Filter</button>
                </div>
            </form>

            <div class="mb-3 text-center data-tabs">
                <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm active">Students</a>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm">Employees</a>
            </div>

            @include('partials.patron-data-toolbar', [
                'registerRoute' => auth()->user()?->can('isAdmin') ? route('students.create') : null,
                'registerLabel' => '+ Register Student',
                'pendingUrl' => route('pending.index', ['tab' => 'students']),
                'importTemplateRoute' => 'students.import.template',
                'importRoute' => 'students.import',
                'exportRoute' => route('students.export', request()->query()),
                'downloadIdsRoute' => route('students.bulk.ids', request()->query()),
            ])

            <div id="students-data-panel" data-loading="false">
                @include('students.partials.list-table', ['students' => $students])
            </div>
        </div>
    </div>
</div>

<template id="students-table-skeleton">
    @include('students.partials.skeleton-table')
</template>
@endsection

@push('scripts')
    <script src="{{ asset('js/students-index.js') }}"></script>
@endpush
