@extends('layouts.sec')

@section('title', 'Employees')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}">
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

            <form id="employees-filter-form" action="{{ route('employees.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search name, ID, department…" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="department" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="position" class="form-select form-select-sm">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-search-filter">Filter</button>
                </div>
            </form>

            <div class="mb-3 text-center data-tabs">
                <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm">Students</a>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm active">Employees</a>
            </div>

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
        'columns' => 8,
        'rows' => 8,
        'loadingLabel' => 'Loading employees…',
        'headers' => ['Profile', 'Last Name', 'First Name', 'Department', 'Position', 'Employee ID', 'Actions', 'Generate ID'],
        'skeletonFirstCol' => 'avatar',
    ])
</template>
@endsection
