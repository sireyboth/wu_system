@props([
    'title' => 'Welcome to',
    'text' => 'Student Management',
    'subtitle' => 'Overview of your application',
])

<div class="mb-8">
    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
        <span>{{ $title }}</span>
        <span class="text-indigo-700">{{ $text }}</span>
    </h1>
    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
        {{ $subtitle }}
    </p>
</div>
