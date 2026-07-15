<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        if (localStorage.theme === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 dark:bg-neutral-950 transition-colors">

    <div class="flex min-h-screen w-full overflow-x-hidden">

    <div class="flex min-h-screen w-full overflow-x-hidden">

        @include('partials.sidebar')
        @include('partials.sidebar')

        <div id="main-content" class="flex-1 min-w-0 p-6 transition-all duration-300">
            @yield('content')
        </div>

    </div>

@stack('scripts')
<script src="https://cdn.jsdelivr.net/gh/ThyrithSor/momentkh@3.0.3/momentkh.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</body>
</html>
