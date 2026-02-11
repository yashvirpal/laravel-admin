{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ ($title ?? 'Dashboard') . ' | ' . config('app.name') }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />

    <meta name="title" content="Yashvir Pal & The Creative Coders">
    <meta name="author" content="Yashvir Pal & The Creative Coders">
    <meta name="description" content="Yashvir Pal and The Creative Coders — building modern, creative web solutions.">
    <meta name="keywords"
        content="Yashvir Pal, The Creative Coders, Web Development, Laravel, PHP, Creative Web Agency">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="{{ asset('backend/css/adminlte.css')}}" as="style" />
    <link rel="stylesheet" href="{{ asset('backend/css/index.css') }}" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="{{ asset('backend/css/overlayscrollbars.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/css/adminlte.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/css/apexcharts.css') }}" />
    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
    <div class="app-wrapper">
        @include('admin.partials.header')
        @include('admin.partials.sidebar')

        <!--begin::App Main-->
        <main class="app-main">
            @include('admin.partials.breadcrumb')
            <div class="app-content">
                <div class="container-fluid">
                    @include('admin.partials.alerts')
                    @yield('content')
                </div>
            </div>
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">D&D <a href="https://yashvirpal.com"
                    class="text-decoration-none">yashvirpal.com</a>.</div>
            <strong> Copyright &copy; 2014-2025&nbsp; {{ config('app.name') }} </strong> All rights reserved.
        </footer>
        <!--end::Footer-->
    </div>
    <script src="{{ asset('backend/js/overlayscrollbars.browser.es6.min.js') }}"></script>
    <script src="{{ asset('backend/js/popper.min.js') }}"></script>
    <script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('backend/js/adminlte.js')}}"></script>
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

            // Disable OverlayScrollbars on mobile devices to prevent touch interference
            const isMobile = window.innerWidth <= 992;

            if (
                sidebarWrapper &&
                OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
                !isMobile
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <script src="{{ asset('backend/js/jquery-3.6.0.min.js') }}"></script>

    @stack('scripts')
</body>

</html>