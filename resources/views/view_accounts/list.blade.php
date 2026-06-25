@extends('layouts.sec')

@section('title', 'User Accounts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/skeleton.css') }}">
@endpush

@section('content')
<div class="data-page accounts-page mt-3">
    <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
            <h4 class="mb-0">User Accounts</h4>
            <a href="{{ route('users.create') }}" class="btn btn-add btn-sm">+ Create Account</a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form id="users-filter-form" method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3">
                <div class="col-md-8">
                    <input type="search" name="search" class="form-control form-control-sm"
                           placeholder="Search name, email, or role…"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-search-filter">Search</button>
                </div>
                @if(request('search'))
                    <div class="col-md-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
                    </div>
                @endif
            </form>

            <div id="users-data-panel"
                 data-hydratable-panel
                 data-loading="false"
                 data-form="#users-filter-form"
                 data-skeleton="#users-table-skeleton"
                 data-pagination=".data-panel-pagination"
                 data-path-match="/view-users">
                @include('view_accounts.partials.list-table', ['users' => $users])
            </div>
        </div>
    </div>
</div>

<template id="users-table-skeleton">
    @include('partials.skeleton-table', [
        'columns' => 5,
        'rows' => 8,
        'loadingLabel' => 'Loading user accounts…',
        'headers' => ['First Name', 'Last Name', 'Email', 'Role', 'Actions'],
        'skeletonFirstCol' => 'text',
    ])
</template>
@endsection
