@extends('layouts.sec')

@section('title', 'Attendance Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance_logs/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}">
@endpush

@section('content')
<div class="attendance-logs-page">
    <div class="al-toolbar">
        <a href="{{ route('attendance_logs.reports.hub') }}" class="export-btn">Patron reports</a>
        <a href="{{ route('attendance_logs.export.pdf', request()->query()) }}" class="export-btn" data-turbo="false">Export PDF</a>
        <a href="{{ route('attendance_logs.export.excel', request()->query()) }}" class="export-btn" data-turbo="false">Export Excel</a>
    </div>

    <div class="al-filters no-bg">
        <form id="attendance-logs-filter-form" method="GET" action="{{ route('attendance_logs.index') }}" class="al-filters-form">
            <div class="al-field">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, course, section...">
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>From</label>
                <input type="date" name="from" value="{{ request('from') }}">
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>To</label>
                <input type="date" name="to" value="{{ request('to') }}">
            </div>
            <div class="al-field">
                <label>Section</label>
                <select name="section">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            <div class="al-field" style="flex:0 1 120px;">
                <label>Year Level</label>
                <select name="year_level">
                    <option value="">All Levels</option>
                    @foreach(['1','2','3','4','5','6'] as $year)
                        <option value="{{ $year }}" {{ request('year_level') == $year ? 'selected' : '' }}>Year {{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="al-field">
                <label>Course</label>
                <select name="course">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course }}" {{ request('course') == $course ? 'selected' : '' }}>{{ $course }}</option>
                    @endforeach
                </select>
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>Status</label>
                <div class="al-status-btns">
                    <button type="submit" name="status" value="IN" class="al-btn-in">IN Only</button>
                    <button type="submit" name="status" value="OUT" class="al-btn-out">OUT Only</button>
                </div>
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>&nbsp;</label>
                <button type="submit" class="btn-search">Search</button>
            </div>
        </form>
    </div>

    <div id="attendance-logs-data-panel"
         data-hydratable-panel
         data-loading="false"
         data-form="#attendance-logs-filter-form"
         data-skeleton="#attendance-logs-table-skeleton"
         data-pagination=".data-panel-pagination"
         data-path-match="/attendance-logs">
        @include('attendance_logs.partials.list-table', ['logs' => $logs])
    </div>
</div>

<template id="attendance-logs-table-skeleton">
    @include('partials.skeleton-table', [
        'columns' => 6,
        'rows' => 8,
        'loadingLabel' => 'Loading attendance logs…',
        'headers' => ['Last Name', 'First Name', 'Course', 'Section', 'Status', 'Scanned At'],
        'skeletonFirstCol' => 'text',
        'tableClass' => 'al-table',
        'wrapClass' => 'data-panel-table-wrap data-panel-table-wrap--loading',
    ])
</template>
@endsection
