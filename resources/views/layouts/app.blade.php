<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset(config('branding.css_path', 'branding/branding.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/layout/dashboard-shell.css') }}">
    @stack('styles')
    @yield('styles')
    @stack('page-styles')
</head>
<body class="layout-dashboard @yield('body_class')" data-turbo="false" style="background: var(--brand-page-bg, #f5f7fa);">
    @include('layouts.partials.navbar')

    @hasSection('banner')
        <div class="pantas-banner">
            @yield('banner')
        </div>
    @endif

    <main class="pantas-main">
        <div class="container-fluid px-3 px-lg-4">
            @hasSection('breadcrumbs')
                @yield('breadcrumbs')
            @elseif (Route::currentRouteName() !== 'home')
                @include('layouts.partials.breadcrumbs', ['items' => $breadcrumbItems ?? []])
            @endif

            @yield('content')
        </div>
    </main>

    @yield('footer')
    @stack('scripts')
    @yield('scripts')

    @auth
        @can('isAdminOrStaff')
            @include('layouts.partials.logout-confirm-dialog')
        @endcan
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @auth
        @can('isAdminOrStaff')
            <script src="{{ asset('js/sidebar.js') }}"></script>
            <script src="{{ asset('js/data-panel.js') }}"></script>
            <script src="{{ asset('js/logout-confirm.js') }}"></script>
            <script src="{{ asset('js/patron-import-labels.js') }}"></script>
            <script src="{{ asset('js/patron-signature-pad.js') }}"></script>
            <script src="{{ asset('js/turbo-admin.js') }}"></script>
        @endcan
    @endauth
</body>
</html>
