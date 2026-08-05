@props(['name', 'title' => 'Are you sure?', 'body' => '', 'closeable' => true])

<div x-data="{ show: false }" x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="if ({{ $closeable ? 'true' : 'false' }}) show = false" x-show="show" x-cloak
    class="fixed inset-0 z-50 grid place-items-center">

    {{-- Backdrop --}}
    <div x-show="show" x-transition.opacity @if ($closeable) x-on:click="show = false" @endif
        class="fixed inset-0 bg-black/60"></div>

    {{-- Panel --}}
    <div x-show="show" x-transition
        class="relative bg-neutral-900 border border-neutral-800 rounded-2xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-semibold text-neutral-100">{{ $title }}</h3>
        <p class="mt-2 text-sm text-neutral-400">{{ $body }}</p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" x-on:click="show = false"
                class="px-4 py-2 rounded-lg text-sm text-neutral-300 hover:bg-neutral-800">
                Cancel
            </button>
            <button type="button" x-on:click="show = false; $dispatch('confirm')"
                class="px-4 py-2 rounded-lg text-sm bg-red-600 hover:bg-red-500 text-white">
                Delete
            </button>
        </div>
    </div>
</div>
