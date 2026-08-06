@props([
    'placement' => 'bottom-end',
    'width' => 'w-44',
    'offset' => 8,
    'matchWidth' => false, // new prop
])

<div x-data="dropdown" data-placement="{{ $placement }}" data-offset="{{ $offset }}"
    data-match-width="{{ $matchWidth ? 'true' : 'false' }}" x-on:click.outside="close()" x-on:scroll.window="close()"
    x-on:resize.window="close()" class="relative block w-full text-left" {{-- changed from inline-block --}}>
    <div x-ref="trigger" x-on:click="toggle()" class="w-full">
        {{ $trigger }}
    </div>

    <template x-teleport="body">
        <div x-show="open" x-transition x-on:click="close()" x-cloak x-bind:style="style"
            class="fixed z-50 {{ $matchWidth ? '' : $width }} bg-white rounded divide-y divide-gray-100 shadow-lg dark:bg-gray-700 dark:divide-gray-600"
            style="display: none;">
            {{ $slot }}
        </div>
    </template>
</div>
