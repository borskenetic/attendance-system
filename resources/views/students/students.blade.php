@extends('layouts.sec')

@section('title', 'Students')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}?v={{ @filemtime(public_path('css/layout/data-pages.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}?v={{ @filemtime(public_path('css/students/students.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}?v={{ @filemtime(public_path('css/layout/skeleton.css')) ?: time() }}">
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

            <div class="data-tabs mb-3" role="tablist">
                <a href="{{ route('students.index') }}" class="data-tab active" aria-current="page">Students</a>
                <a href="{{ route('employees.index') }}" class="data-tab">Employees</a>
            </div>

            <form id="students-filter-form" action="{{ route('students.index') }}" method="GET" class="patron-filter-bar mb-3">
                <input type="text" name="search" class="form-control form-control-sm patron-filter-search"
                       placeholder="Search name, ID, course…" value="{{ request('search') }}">
                <div class="patron-filter-selects">
                    @include('partials.program-course-filter', ['programsByLevel' => $programsByLevel])
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All Years</option>
                        @foreach(array_merge(
                            \App\Support\SchoolLevel::yearOptions(\App\Support\SchoolLevel::COLLEGE),
                            \App\Support\SchoolLevel::yearOptions(\App\Support\SchoolLevel::SENIOR_HIGH),
                            \App\Support\SchoolLevel::yearOptions(\App\Support\SchoolLevel::JUNIOR_HIGH)
                        ) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm btn-search-filter">Filter</button>
            </form>

            @include('partials.patron-data-toolbar', [
                'registerRoute' => auth()->user()?->can('isAdmin') ? route('students.create') : null,
                'registerLabel' => '+ Register Student',
                'pendingUrl' => route('pending.index', ['tab' => 'students']),
                'importTemplateRoute' => 'students.import.template',
                'importRoute' => 'students.import',
                'exportRoute' => route('students.export', request()->query()),
                'downloadIdsRoute' => route('students.bulk.ids', request()->query()),
            ])

            <div id="students-data-panel"
                 data-hydratable-panel
                 data-loading="false"
                 data-form="#students-filter-form"
                 data-skeleton="#students-table-skeleton"
                 data-pagination=".data-panel-pagination"
                 data-path-match="/students">
                @include('students.partials.list-table', ['students' => $students])
            </div>
        </div>
    </div>
</div>

<template id="students-table-skeleton">
    @include('partials.skeleton-table', [
        'columns' => 5,
        'rows' => 8,
        'loadingLabel' => 'Loading students…',
        'headers' => ['Student', 'Student ID', 'Course', 'Year', 'Actions'],
        'skeletonFirstCol' => 'avatar',
    ])
</template>
@endsection
