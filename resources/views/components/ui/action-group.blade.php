@props(['title' => 'Schedules'])

<div class="flex items-center justify-between gap-x-4 mb-4">
    <div class="inline-flex items-center gap-1">
        <a href="{{ route('dashboard') }}" class="text-blue-400">Home</a>
        <span>/</span>
        <h2>{{ $title }}</h2>
    </div>

    <div class="inline-flex md:flex-row items-center gap-2 text-wrap">
        {{ $slot }}
    </div>
</div>
