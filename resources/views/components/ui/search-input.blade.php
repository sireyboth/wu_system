@props(['hint' => 'Search...'])

<div class="relative w-full md:w-96 group">
    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
        <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
        </svg>
    </div>
    <input type="text" x-model="search" x-on:input="onSearchInput()"
        {{ $attributes->merge([
            'class' =>
                'block w-full p-2 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all',
        ]) }}
        placeholder="{{ $hint }}">
</div>
