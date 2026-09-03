@extends('layouts.sec')

@section('title', 'Employees')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}?v={{ @filemtime(public_path('css/layout/data-pages.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}?v={{ @filemtime(public_path('css/students/students.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}?v={{ @filemtime(public_path('css/layout/skeleton.css')) ?: time() }}">
    <style>
        .data-page .data-tabs { display: inline-flex; gap: .25rem; padding: .25rem; background: rgba(34,51,59,.05); border: 1px solid rgba(34,51,59,.1); margin-bottom: 1rem; }
        .data-page .data-tab { display: inline-flex; align-items: center; justify-content: center; min-width: 7rem; padding: .4rem 1rem; font-size: .8125rem; font-weight: 600; color: rgba(34,51,59,.75) !important; text-decoration: none !important; }
        .data-page .data-tab.active { background: var(--brand-button-bg, #932c27); color: #fff !important; }
        .data-page .patron-person { display: flex !important; align-items: center !important; gap: .7rem !important; }
        .data-page .patron-avatar { width: 40px !important; height: 40px !important; object-fit: cover; flex: 0 0 40px; border: 1px solid rgba(34,51,59,.12); background: #f3f4f6; }
        .data-page .patron-avatar--empty { display: inline-flex !important; align-items: center; justify-content: center; font-weight: 700; color: rgba(34,51,59,.55); }
        .data-page .patron-person-name { font-weight: 600; line-height: 1.25; }
        .data-page .patron-person-meta { font-size: .75rem; color: rgba(34,51,59,.55); }
        .data-page .patron-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; margin-bottom: 1rem; padding: .65rem .85rem; background: #fff; border: 1px solid rgba(34,51,59,.1); }
        .data-page .patron-toolbar-primary, .data-page .patron-toolbar-secondary { display: flex; flex-wrap: wrap; gap: .45rem; align-items: center; }
        .data-page .patron-toolbar-link { color: #22333b !important; border: 1px solid rgba(34,51,59,.18); background: #fff; font-weight: 600; text-decoration: none !important; }
        .data-page .patron-action-btn { font-weight: 600; color: #22333b !important; background: #fff !important; border: 1px solid rgba(34,51,59,.18) !important; }
        .data-page .patron-list-table tbody td { text-align: left; vertical-align: middle !important; }
        .data-page .patron-empty { text-align: center !important; padding: 2rem 1rem !important; color: rgba(34,51,59,.55); }
        .data-page .patron-filter-bar .form-select { width: auto !important; margin: 0 !important; }
    </style>
@endsection

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

            <div class="data-tabs" role="tablist">
                <a href="{{ route('students.index') }}" class="data-tab">Students</a>
                <a href="{{ route('employees.index') }}" class="data-tab active" aria-current="page">Employees</a>
            </div>

            <form id="employees-filter-form" action="{{ route('employees.index') }}" method="GET" class="row g-2 align-items-stretch mb-3">
                <div class="col-12 col-lg-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search name, ID, department…" value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-5 col-lg-3">
                    <select name="department" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="position" class="form-select form-select-sm">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-search-filter">Filter</button>
                </div>
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
