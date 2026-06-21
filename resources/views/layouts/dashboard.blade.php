<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 dark:bg-neutral-950 transition-colors">

    @include('partials.sidebar')

    <main class="p-4 sm:ml-64">
        @yield('content')
    </main>
@stack('scripts')
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>
