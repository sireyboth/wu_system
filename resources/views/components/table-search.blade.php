<form id="{{ $formId }}" method="GET" action="{{ route($route) }}" class="relative w-full md:w-96 group">
    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
        <x-icon name="search" class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors" />
    </div>

    <x-text-input id="{{ $inputId }}" name="search" value="{{ request('search') }}"
        class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
        placeholder="{{ $placeholder }}" autocomplete="off" />
</form>
