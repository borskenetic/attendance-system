@php
    $linkActive = fn (array $patterns) => collect($patterns)->contains(fn ($p) => request()->routeIs($p)) ? 'active' : '';
    $dropActive = fn (array $patterns) => collect($patterns)->contains(fn ($p) => request()->routeIs($p)) ? 'active' : '';
@endphp

<button id="customMenuToggle" class="sidebar-trigger" type="button" aria-label="Open menu">
    <span></span>
    <span></span>
    <span></span>
</button>

<button id="sidebarCollapseToggle" class="sidebar-collapse-toggle" type="button" aria-label="Collapse sidebar" aria-expanded="true">
    <i class="bi bi-chevron-left sidebar-collapse-toggle__icon" aria-hidden="true"></i>
</button>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<aside class="pantas-header">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand" title="{{ config('app.name') }}">
            <img src="{{ asset('images/pantasLogo.png') }}" alt="{{ config('app.name') }}" class="header-logo-img">
        </a>

        <button id="customMenuClose" class="close-btn" type="button" aria-label="Close menu">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>

    <div class="sidebar-body">
        <p class="sidebar-menu-label" aria-hidden="true">Menu</p>

        <nav id="routeWrapper" class="responsive-nav" aria-label="Main navigation">
            <a
                href="{{ route('home') }}"
                class="sidebar-link {{ $linkActive(['home']) }}"
                data-tooltip="Home"
                @if($linkActive(['home'])) aria-current="page" @endif
            >
                <span class="sidebar-link__main">
                    <i class="bi bi-house-door sidebar-link__icon" aria-hidden="true"></i>
                    <span class="sidebar-link__label">Home</span>
                </span>
            </a>

            @auth
                @can('isAdminOrStaff')
                    <div class="nav-dropdown" data-sidebar-section="attendance">
                        <button
                            type="button"
                            class="nav-dropdown-button sidebar-link {{ $dropActive(['attendance.scan', 'attendance.process', 'attendance.section', 'attendance_logs.index', 'attendance.changeVideo', 'attendance.uploadVideo', 'attendance.feedback.settings*', 'attendance.section.settings*']) }}"
                            aria-expanded="false"
                            aria-controls="sidebar-attendance-menu"
                            data-tooltip="Attendance"
                        >
                            <span class="sidebar-link__main">
                                <i class="bi bi-clipboard-check sidebar-link__icon" aria-hidden="true"></i>
                                <span class="sidebar-link__label">Attendance</span>
                            </span>
                            <i class="bi bi-chevron-down sidebar-link__chevron" aria-hidden="true"></i>
                        </button>
                        <div id="sidebar-attendance-menu" class="nav-dropdown-content" hidden>
                            <a href="{{ route('attendance.scan') }}" target="_blank" rel="noopener" class="{{ $linkActive(['attendance.scan']) }}" @if($linkActive(['attendance.scan'])) aria-current="page" @endif>Attendance</a>
                            <a href="{{ route('attendance_logs.index') }}" class="{{ $linkActive(['attendance_logs.index']) }}" @if($linkActive(['attendance_logs.index'])) aria-current="page" @endif>Attendance Logs</a>
                            <a href="{{ route('attendance.changeVideo') }}" class="{{ $linkActive(['attendance.changeVideo', 'attendance.uploadVideo']) }}" @if($linkActive(['attendance.changeVideo', 'attendance.uploadVideo'])) aria-current="page" @endif>Manage Video</a>
                            <a href="{{ route('attendance.section.settings') }}" class="{{ $linkActive(['attendance.section.settings*']) }}" @if($linkActive(['attendance.section.settings*'])) aria-current="page" @endif>Section Picker</a>
                            <a href="{{ route('attendance.feedback.settings') }}" class="{{ $linkActive(['attendance.feedback.settings*']) }}" @if($linkActive(['attendance.feedback.settings*'])) aria-current="page" @endif>Logout Feedback</a>
                        </div>
                    </div>

                    <div class="nav-dropdown" data-sidebar-section="data">
                        <button
                            type="button"
                            class="nav-dropdown-button sidebar-link {{ $dropActive(['students.index', 'students.create', 'students.edit', 'students.report', 'employees.*', 'pending.index', 'pending.employees', 'students.pending']) }}"
                            aria-expanded="false"
                            aria-controls="sidebar-data-menu"
                            data-tooltip="Data"
                        >
                            <span class="sidebar-link__main">
                                <i class="bi bi-database sidebar-link__icon" aria-hidden="true"></i>
                                <span class="sidebar-link__label">Data</span>
                            </span>
                            <i class="bi bi-chevron-down sidebar-link__chevron" aria-hidden="true"></i>
                        </button>
                        <div id="sidebar-data-menu" class="nav-dropdown-content" hidden>
                            <a href="{{ route('students.index') }}" class="{{ $linkActive(['students.index', 'students.create', 'students.edit', 'students.report']) }}" @if($linkActive(['students.index', 'students.create', 'students.edit', 'students.report'])) aria-current="page" @endif>Students</a>
                            <a href="{{ route('employees.index') }}" class="{{ $linkActive(['employees.index', 'employees.create', 'employees.edit']) }}" @if($linkActive(['employees.index', 'employees.create', 'employees.edit'])) aria-current="page" @endif>Employees</a>
                        </div>
                    </div>

                    <div class="nav-dropdown" data-sidebar-section="communication">
                        <button
                            type="button"
                            class="nav-dropdown-button sidebar-link {{ $dropActive(['feedback.*', 'sms.*']) }}"
                            aria-expanded="false"
                            aria-controls="sidebar-communication-menu"
                            data-tooltip="Communication"
                        >
                            <span class="sidebar-link__main">
                                <i class="bi bi-chat-dots sidebar-link__icon" aria-hidden="true"></i>
                                <span class="sidebar-link__label">Communication</span>
                            </span>
                            <i class="bi bi-chevron-down sidebar-link__chevron" aria-hidden="true"></i>
                        </button>
                        <div id="sidebar-communication-menu" class="nav-dropdown-content" hidden>
                            <a href="{{ route('feedback.index') }}" class="{{ $linkActive(['feedback.index']) }}" @if($linkActive(['feedback.index'])) aria-current="page" @endif>Feedback</a>
                            <a href="{{ route('sms.page') }}" class="{{ $linkActive(['sms.page', 'sms.send']) }}" @if($linkActive(['sms.page', 'sms.send'])) aria-current="page" @endif>SMS blast</a>
                            <a href="{{ route('sms.scanMessage') }}" class="{{ $linkActive(['sms.scanMessage', 'sms.scanMessage.update']) }}" @if($linkActive(['sms.scanMessage', 'sms.scanMessage.update'])) aria-current="page" @endif>Scanner message</a>
                        </div>
                    </div>

                    @can('isAdmin')
                        <div class="nav-dropdown" data-sidebar-section="admin">
                            <button
                                type="button"
                                class="nav-dropdown-button sidebar-link {{ $dropActive(['users.*', 'prospectus.*']) }}"
                                aria-expanded="false"
                                aria-controls="sidebar-admin-menu"
                                data-tooltip="Admin"
                            >
                                <span class="sidebar-link__main">
                                    <i class="bi bi-shield-lock sidebar-link__icon" aria-hidden="true"></i>
                                    <span class="sidebar-link__label">Admin</span>
                                </span>
                                <i class="bi bi-chevron-down sidebar-link__chevron" aria-hidden="true"></i>
                            </button>
                            <div id="sidebar-admin-menu" class="nav-dropdown-content" hidden>
                                <a href="{{ route('prospectus.index') }}" class="{{ $linkActive(['prospectus.*']) }}" @if($linkActive(['prospectus.*'])) aria-current="page" @endif>School Setup</a>
                                <a href="{{ route('users.create') }}" class="{{ $linkActive(['users.create', 'users.store']) }}" @if($linkActive(['users.create', 'users.store'])) aria-current="page" @endif>Create Account</a>
                                <a href="{{ route('users.index') }}" class="{{ $linkActive(['users.index', 'users.edit']) }}" @if($linkActive(['users.index', 'users.edit'])) aria-current="page" @endif>View Accounts</a>
                            </div>
                        </div>
                    @endcan
                @endcan
            @else
                <a
                    href="{{ route('patron.register') }}"
                    class="sidebar-link sidebar-link--register {{ $linkActive(['patron.register', 'pending.store', 'pendingEmployee.store']) }}"
                    data-tooltip="Register"
                    @if($linkActive(['patron.register', 'pending.store', 'pendingEmployee.store'])) aria-current="page" @endif
                >
                    <span class="sidebar-link__main">
                        <i class="bi bi-person-plus sidebar-link__icon" aria-hidden="true"></i>
                        <span class="sidebar-link__label">Register</span>
                    </span>
                </a>
                <a href="{{ route('login') }}" class="sidebar-link sidebar-link--login" data-tooltip="Login">
                    <span class="sidebar-link__main">
                        <i class="bi bi-box-arrow-in-right sidebar-link__icon" aria-hidden="true"></i>
                        <span class="sidebar-link__label">Login</span>
                    </span>
                </a>
            @endauth
        </nav>
    </div>

    @auth
        @can('isAdminOrStaff')
            <div class="sidebar-footer">
                <div class="sidebar-footer__meta">
                    <span class="sidebar-footer__dot" aria-hidden="true"></span>
                    <span class="sidebar-footer__label">{{ config('app.name', 'BCCI Library') }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="sidebar-footer__form" id="sidebarLogoutForm" data-turbo="false">
                    @csrf
                    <button type="button" class="sidebar-logout" data-logout-confirm data-tooltip="Logout">
                        <span class="sidebar-link__main">
                            <i class="bi bi-box-arrow-right sidebar-link__icon" aria-hidden="true"></i>
                            <span class="sidebar-link__label">Logout</span>
                        </span>
                    </button>
                </form>
            </div>
        @endcan
    @endauth
</aside>
