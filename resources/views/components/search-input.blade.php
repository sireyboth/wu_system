@props(['placeholder' => 'Search...'])

<div {{ $attributes->except(['class']) }} class="relative w-full max-w-md">
    <x-icon name="search" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500" />
    <input type="text" {{ $attributes->only('x-model') }} placeholder="{{ $placeholder }}"
        class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all" />
</div>
