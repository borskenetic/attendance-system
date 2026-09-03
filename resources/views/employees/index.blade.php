@extends('layouts.sec')

@section('title', 'Employees')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}?v={{ @filemtime(public_path('css/layout/data-pages.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}?v={{ @filemtime(public_path('css/students/students.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}?v={{ @filemtime(public_path('css/layout/skeleton.css')) ?: time() }}">
@endpush

@section('content')
<div class="data-page mt-3">
    <div class="card">
        <div class="card-header text-center">
            <h4>Registered Employees</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="data-tabs mb-3" role="tablist">
                <a href="{{ route('students.index') }}" class="data-tab">Students</a>
                <a href="{{ route('employees.index') }}" class="data-tab active" aria-current="page">Employees</a>
            </div>

            <form id="employees-filter-form" action="{{ route('employees.index') }}" method="GET" class="patron-filter-bar mb-3">
                <input type="text" name="search" class="form-control form-control-sm patron-filter-search"
                       placeholder="Search name, ID, department…" value="{{ request('search') }}">
                <div class="patron-filter-selects">
                    <select name="department" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                    <select name="position" class="form-select form-select-sm">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm btn-search-filter">Filter</button>
            </form>

            @include('partials.patron-data-toolbar', [
                'registerRoute' => route('employees.create'),
                'registerLabel' => '+ Register Employee',
                'pendingUrl' => route('pending.index', ['tab' => 'employees']),
                'importTemplateRoute' => 'employees.import.template',
                'importRoute' => 'employees.import',
                'exportRoute' => route('employees.export', request()->query()),
                'downloadIdsRoute' => route('employees.bulk.ids', request()->query()),
            ])

            <div id="employees-data-panel"
                 data-hydratable-panel
                 data-loading="false"
                 data-form="#employees-filter-form"
                 data-skeleton="#employees-table-skeleton"
                 data-pagination=".data-panel-pagination"
                 data-path-match="/employees">
                @include('employees.partials.list-table', ['faculty' => $faculty])
            </div>
        </div>
    </div>
</div>

<template id="employees-table-skeleton">
    @include('partials.skeleton-table', [
        'columns' => 5,
        'rows' => 8,
        'loadingLabel' => 'Loading employees…',
        'headers' => ['Employee', 'Employee ID', 'Department', 'Position', 'Actions'],
        'skeletonFirstCol' => 'avatar',
    ])
</template>
@endsection
