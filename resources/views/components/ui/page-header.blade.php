@props(['welcome' => 'សូមស្វាគមន៍មកកាន់ទំព័រ', 'title' => 'Home', 'subtitle' => 'Overview of your application'])

<div class="mb-6">
    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
        {{ $welcome }} <span class="text-indigo-700">{{ $title }}</span>
    </h1>

    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
        {{ $subtitle }}
    </p>
</div>
