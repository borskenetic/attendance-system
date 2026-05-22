@extends('layouts.sec')

@section('title', 'Employees')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/students/students.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}">
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

            <form action="{{ route('employees.index') }}" method="GET" class="row g-2 mb-3">
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle patron-list-table">
                    <thead>
                        <tr>
                            <th scope="col">Profile</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Department</th>
                            <th scope="col">Position</th>
                            <th scope="col">Employee ID</th>
                            <th scope="col">Actions</th>
                            <th scope="col">Generate ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faculty as $employee)
                            <tr>
                                <td>
                                    @if($employee->formal_picture)
                                        <img src="{{ asset($employee->formal_picture) }}" width="80" class="rounded" alt="">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>{{ $employee->lastname }}</td>
                                <td>{{ $employee->firstname }}</td>
                                <td>{{ $employee->department }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>{{ $employee->employee_id ?? $employee->qrcode }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Options</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('employees.edit', $employee->id) }}">Edit</a></li>
                                            <li>
                                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Delete this employee?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item" type="submit">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Generate</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('employees.idcard.front', $employee->id) }}" target="_blank">Front</a></li>
                                            <li><a class="dropdown-item" href="{{ route('employees.idcard.back', $employee->id) }}" target="_blank">Back</a></li>
                                            <li><a class="dropdown-item" href="{{ route('employees.idcard.download', $employee->id) }}">Download ZIP</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No employees found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $faculty->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
