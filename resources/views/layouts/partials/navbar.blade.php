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
    <span></span>
    <span></span>
</button>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<aside class="pantas-header">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <img src="{{ asset('images/pantasLogo.png') }}" alt="{{ config('app.name') }}" class="header-logo-img">
        </a>

        <button id="customMenuClose" class="close-btn" type="button" aria-label="Close menu">&times;</button>
    </div>

    <nav id="routeWrapper" class="responsive-nav" aria-label="Main navigation">
        <a href="{{ route('home') }}" class="btn0 btn-sm {{ $linkActive(['home']) }}">Home</a>

        @auth
            @can('isAdminOrStaff')
                <div class="nav-dropdown" data-sidebar-section="attendance">
                    <button type="button" class="nav-dropdown-button {{ $dropActive(['attendance.scan', 'attendance.process', 'attendance.section', 'attendance_logs.index', 'attendance.changeVideo', 'attendance.uploadVideo', 'attendance.feedback.settings*', 'attendance.section.settings*']) }}" aria-expanded="false" aria-controls="sidebar-attendance-menu">
                        Attendance
                    </button>
                    <div id="sidebar-attendance-menu" class="nav-dropdown-content" hidden>
                        <a href="{{ route('attendance.scan') }}" target="_blank" rel="noopener" class="{{ $linkActive(['attendance.scan']) }}">Attendance</a>
                        <a href="{{ route('attendance_logs.index') }}" class="{{ $linkActive(['attendance_logs.index']) }}">Attendance Logs</a>
                        <a href="{{ route('attendance.changeVideo') }}" class="{{ $linkActive(['attendance.changeVideo', 'attendance.uploadVideo']) }}">Manage Video</a>
                        <a href="{{ route('attendance.section.settings') }}" class="{{ $linkActive(['attendance.section.settings*']) }}">Section Picker</a>
                        <a href="{{ route('attendance.feedback.settings') }}" class="{{ $linkActive(['attendance.feedback.settings*']) }}">Logout Feedback</a>
                    </div>
                </div>

                <div class="nav-dropdown" data-sidebar-section="data">
                    <button type="button" class="nav-dropdown-button {{ $dropActive(['students.index', 'students.create', 'students.edit', 'students.report', 'employees.*', 'pending.index', 'pending.employees', 'students.pending']) }}" aria-expanded="false" aria-controls="sidebar-data-menu">
                        Data
                    </button>
                    <div id="sidebar-data-menu" class="nav-dropdown-content" hidden>
                        <a href="{{ route('students.index') }}" class="{{ $linkActive(['students.index', 'students.create', 'students.edit', 'students.report']) }}">Students</a>
                        <a href="{{ route('employees.index') }}" class="{{ $linkActive(['employees.index', 'employees.create', 'employees.edit']) }}">Employees</a>
                    </div>
                </div>

                <div class="nav-dropdown" data-sidebar-section="communication">
                    <button type="button" class="nav-dropdown-button {{ $dropActive(['feedback.*', 'sms.*']) }}" aria-expanded="false" aria-controls="sidebar-communication-menu">
                        Communication
                    </button>
                    <div id="sidebar-communication-menu" class="nav-dropdown-content" hidden>
                        <a href="{{ route('feedback.index') }}" class="{{ $linkActive(['feedback.index']) }}">Feedback</a>
                        <a href="{{ route('sms.page') }}" class="{{ $linkActive(['sms.page', 'sms.send']) }}">SMS blast</a>
                        <a href="{{ route('sms.scanMessage') }}" class="{{ $linkActive(['sms.scanMessage', 'sms.scanMessage.update']) }}">Scanner message</a>
                    </div>
                </div>

                @can('isAdmin')
                    <div class="nav-dropdown" data-sidebar-section="admin">
                        <button type="button" class="nav-dropdown-button {{ $dropActive(['users.*', 'prospectus.*']) }}" aria-expanded="false" aria-controls="sidebar-admin-menu">
                            Admin
                        </button>
                        <div id="sidebar-admin-menu" class="nav-dropdown-content" hidden>
                            <a href="{{ route('prospectus.index') }}" class="{{ $linkActive(['prospectus.*']) }}">Prospectus Manager</a>
                            <a href="{{ route('users.create') }}" class="{{ $linkActive(['users.create', 'users.store']) }}">Create Account</a>
                            <a href="{{ route('users.index') }}" class="{{ $linkActive(['users.index', 'users.edit']) }}">View Accounts</a>
                        </div>
                    </div>
                @endcan

                <form action="{{ route('logout') }}" method="POST" class="d-inline mb-0">
                    @csrf
                    <button type="submit" class="btn5">Logout</button>
                </form>
            @endcan
        @else
            <a href="{{ route('patron.register') }}" class="btn2 btn-sm {{ $linkActive(['patron.register', 'pending.store', 'pendingEmployee.store']) }}">Register</a>
            <a href="{{ route('login') }}" class="btn5 btn-sm" style="text-decoration:none;display:inline-block;">Login</a>
        @endauth
    </nav>
</aside>

<script>
(function () {
    const toggleBtn = document.getElementById('customMenuToggle');
    const closeBtn = document.getElementById('customMenuClose');
    const collapseBtn = document.getElementById('sidebarCollapseToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebar = document.querySelector('.pantas-header');
    if (!toggleBtn || !sidebar) return;

    const closeSidebar = () => document.body.classList.remove('sidebar-open');
    const setCollapsed = (collapsed) => {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        collapseBtn?.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        collapseBtn?.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        localStorage.setItem('sidebar-collapsed', collapsed ? 'true' : 'false');
    };

    setCollapsed(localStorage.getItem('sidebar-collapsed') === 'true');

    toggleBtn.addEventListener('click', () => document.body.classList.add('sidebar-open'));
    collapseBtn?.addEventListener('click', () => {
        setCollapsed(!document.body.classList.contains('sidebar-collapsed'));
    });
    closeBtn?.addEventListener('click', closeSidebar);
    backdrop?.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 769) closeSidebar();
    });

    const dropdowns = Array.from(document.querySelectorAll('.pantas-header .nav-dropdown'));
    let openDropdown = null;

    const setOpenDropdown = (sectionName) => {
        openDropdown = sectionName;

        dropdowns.forEach((dropdown) => {
            const isOpen = dropdown.dataset.sidebarSection === openDropdown;
            const button = dropdown.querySelector('.nav-dropdown-button');
            const content = dropdown.querySelector('.nav-dropdown-content');

            dropdown.classList.toggle('open', isOpen);
            button?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (content) content.hidden = !isOpen;
        });
    };

    const activeDropdown = dropdowns.find((dropdown) => {
        return dropdown.querySelector('.nav-dropdown-button.active, .nav-dropdown-content .active');
    });

    if (activeDropdown) setOpenDropdown(activeDropdown.dataset.sidebarSection);

    dropdowns.forEach((dropdown) => {
        const btn = dropdown.querySelector('.nav-dropdown-button');
        if (!btn) return;

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const nextSection = dropdown.dataset.sidebarSection;
            setOpenDropdown(openDropdown === nextSection ? null : nextSection);
        });
    });
})();
</script>
