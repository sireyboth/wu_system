@props(['title' => 'Schedules', 'route' => 'dashboard'])

<div class="flex items-center justify-between gap-x-4 mb-4 overflow-x-auto">
    <div class="inline-flex items-center gap-1">
        <a href="{{ route($route) }}" class="text-blue-400">Home</a>
        <span>/</span>
        <h2>{{ $title }}</h2>
    </div>

    <div class="inline-flex md:flex-row items-center gap-2 text-wrap">
        {{ $slot }}
    </div>
</div>
