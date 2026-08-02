{{--
    Generic dropdown. Pass the trigger element via the "trigger" slot and the
    panel contents via the default slot. Reused for the top Actions menu, the
    brand Filter menu, and each row's Show/Edit/Delete menu.

    NOTE: pass a full utility class to "width" (e.g. "w-44"), not a bare
    number — Tailwind can't detect dynamically-built class strings at build time.
--}}
@props([
    'align' => 'right', // left | right
    'width' => 'w-44',
])

<div x-data="{ open: false }" x-on:click.outside="open = false" class="relative inline-block text-left">
    <div x-on:click="open = !open">
        {{ $trigger }}
    </div>

    <div x-show="open" x-transition x-on:click="open = false" x-cloak
        class="absolute z-30 mt-2 {{ $width }} bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 {{ $align === 'right' ? 'right-0' : 'left-0' }}"
        style="display: none;">
        {{ $slot }}
    </div>
</div>
