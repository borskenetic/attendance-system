@extends('layouts.sec')

@section('title', 'Create User Account')
@section('body_class', 'account-create-body')
@section('breadcrumbs')
    <span class="d-none"></span>
@endsection

@push('styles')
    <style>
        .account-create-body {
            --account-panel-bg: #ffffff;
            --account-panel-border: #d9e2d3;
            --account-text: #22333b;
            --account-muted: #607268;
            --account-green: #287728;
            --account-green-dark: #e9f5e3;
        }

        .account-create-body,
        .account-create-body .pantas-main {
            background: #ffffff !important;
        }

        .account-create-body .pantas-main > .container-fluid {
            min-height: calc(100vh - 96px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 1.25rem;
            padding-bottom: 1.25rem;
        }

        .account-create-page {
            width: min(720px, 100%);
            margin: 0 auto;
            color: var(--account-text);
        }

        .account-create-card {
            background: var(--account-panel-bg);
            border: 1px solid var(--account-panel-border);
            border-radius: 12px;
            box-shadow: 0 14px 34px rgba(34, 51, 59, 0.08);
            padding: clamp(1.25rem, 3vw, 2rem);
        }

        .account-create-header {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .account-create-icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: var(--account-green-dark);
            color: var(--account-green);
            font-size: 1.1rem;
        }

        .account-create-title {
            margin: 0;
            color: var(--account-text);
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .account-create-subtitle {
            margin: 0.35rem 0 0;
            color: var(--account-muted);
            font-size: 0.95rem;
            line-height: 1.25;
        }

        .account-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .account-form-field-full {
            grid-column: 1 / -1;
        }

        .account-create-page .form-label {
            color: var(--account-muted);
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .account-input-wrap {
            position: relative;
        }

        .account-input-icon,
        .account-select-arrow,
        .account-password-toggle {
            position: absolute;
            top: 23px;
            transform: translateY(-50%);
            color: #7b8d84;
            pointer-events: none;
            z-index: 2;
        }

        .account-input-icon {
            left: 0.9rem;
            font-size: 0.95rem;
        }

        .account-select-arrow,
        .account-password-toggle {
            right: 0.9rem;
            font-size: 0.9rem;
        }

        .account-create-page .form-control,
        .account-create-page .form-select {
            min-height: 46px;
            background-color: #ffffff;
            border: 1px solid #ccd8cf;
            border-radius: 10px;
            color: var(--account-text);
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.65rem 2.75rem 0.65rem 2.55rem;
            box-shadow: none;
        }

        .account-create-page .form-control::placeholder {
            color: #9aa79f;
            opacity: 1;
        }

        .account-create-page .form-control:focus,
        .account-create-page .form-select:focus {
            background-color: #ffffff;
            border-color: #8fce58;
            color: var(--account-text);
            box-shadow: 0 0 0 0.2rem rgba(143, 206, 88, 0.2);
        }

        .account-create-page .form-select {
            appearance: none;
            background-image: none;
        }

        .account-create-page .form-select option {
            color: #20201f;
            background: #ffffff;
        }

        .account-create-actions {
            display: grid;
            grid-template-columns: minmax(180px, 1fr) 180px;
            gap: 0.9rem;
            margin-top: 1.5rem;
        }

        .account-create-page .btn {
            min-height: 46px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1;
        }

        .account-create-page .btn-create-account {
            background: var(--account-green);
            border-color: var(--account-green);
            color: #ffffff;
        }

        .account-create-page .btn-create-account:hover,
        .account-create-page .btn-create-account:focus {
            background: #236b23;
            border-color: #236b23;
            color: #ffffff;
        }

        .account-create-page .btn-view-users {
            color: var(--account-green);
            border: 1px solid #ccd8cf;
            background: transparent;
        }

        .account-create-page .btn-view-users:hover,
        .account-create-page .btn-view-users:focus {
            color: var(--account-green);
            border-color: #8fce58;
            background: rgba(143, 206, 88, 0.12);
        }

        .account-create-page .alert {
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .account-create-page .invalid-feedback {
            color: #b42318;
            font-weight: 600;
        }

        .account-create-page .form-control.is-invalid,
        .account-create-page .form-select.is-invalid {
            border-color: #dc3545;
        }

        @media (max-width: 991.98px) {
            .account-create-card {
                padding: 1.5rem;
            }

            .account-create-title {
                font-size: 1.25rem;
            }

            .account-create-subtitle,
            .account-create-page .form-label,
            .account-create-page .form-control,
            .account-create-page .form-select,
            .account-create-page .btn {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 767.98px) {
            .account-create-body .pantas-main > .container-fluid {
                align-items: flex-start;
            }

            .account-create-header {
                align-items: flex-start;
                margin-bottom: 1.25rem;
            }

            .account-create-icon {
                width: 44px;
                height: 44px;
            }

            .account-form-grid,
            .account-create-actions {
                grid-template-columns: 1fr;
            }

            .account-create-page .form-control,
            .account-create-page .form-select,
            .account-create-page .btn {
                min-height: 44px;
            }

            .account-input-icon,
            .account-select-arrow,
            .account-password-toggle {
                top: 22px;
            }
        }

        @media (max-width: 420px) {
            .account-create-card {
                padding: 1.25rem;
                border-radius: 10px;
            }

            .account-create-header {
                gap: 0.9rem;
            }

            .account-create-title {
                font-size: 1.15rem;
            }

            .account-create-subtitle {
                font-size: 0.88rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="data-page accounts-page account-create-page">
    <div class="account-create-card">
        <div class="account-create-header">
            <div class="account-create-icon" aria-hidden="true">
                <i class="bi bi-person-plus"></i>
            </div>
            <div>
                <h4 class="account-create-title">Create account</h4>
                <p class="account-create-subtitle">Fill in the details below to get started</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="account-form-grid">
                <div>
                    <label for="fname" class="form-label">First name</label>
                    <div class="account-input-wrap">
                        <i class="bi bi-person account-input-icon" aria-hidden="true"></i>
                        <input id="fname" type="text" name="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname') }}" placeholder="Jane" required>
                        @error('fname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="lname" class="form-label">Last name</label>
                    <div class="account-input-wrap">
                        <i class="bi bi-person account-input-icon" aria-hidden="true"></i>
                        <input id="lname" type="text" name="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname') }}" placeholder="Doe" required>
                        @error('lname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="account-form-field-full">
                    <label for="email" class="form-label">Email address</label>
                    <div class="account-input-wrap">
                        <i class="bi bi-envelope account-input-icon" aria-hidden="true"></i>
                        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="jane@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="password" class="form-label">Password</label>
                    <div class="account-input-wrap">
                        <i class="bi bi-lock account-input-icon" aria-hidden="true"></i>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="********" required>
                        <i class="bi bi-eye account-password-toggle" aria-hidden="true"></i>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="role" class="form-label">Role</label>
                    <div class="account-input-wrap">
                        <i class="bi bi-shield-check account-input-icon" aria-hidden="true"></i>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="faculty" {{ old('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                        </select>
                        <i class="bi bi-chevron-down account-select-arrow" aria-hidden="true"></i>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="account-create-actions">
                <button type="submit" class="btn btn-create-account">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                    <span>Create account</span>
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-view-users">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    <span>View users</span>
                    <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
