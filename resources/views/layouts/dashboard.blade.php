<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        let {
            classList
        } = document.documentElement
        if (localStorage.theme === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            classList.add('dark');
        } else {
            classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')

    @ddfsnStyles
    @ddfsnAppearance
</head>

<body x-data class="bg-neutral-50 dark:bg-neutral-950 transition-colors">

    <div class="flex min-h-screen w-full overflow-x-hidden">

        {{-- @include('partials.sidebar') --}}
        @include('partials.side-menu')

        <div id="main-content"
            class="flex-1 min-w-0 p-6 transition-all duration-300 ease-in-out [[body.sidebar-open]_&]:sm:ml-64">
            @yield('content')
        </main>

    </div>

    @ddfsnScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.16.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/ThyrithSor/momentkh@3.0.3/momentkh.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>


</html>
