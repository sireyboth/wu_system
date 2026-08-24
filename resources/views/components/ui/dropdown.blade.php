@props([
    'placement' => 'bottom-start',
    'width' => 'w-44',
    'offset' => 8,
])

<div class="flex justify-center">
    <div x-data="eventDropdown({
        placement: '{{ $placement }}',
        offset: {{ $offset }},
    })" class="relative block w-full text-left" x-on:keydown.escape.window="close()"
        x-init="init()">
        {{-- Trigger --}}
        <div x-ref="trigger" x-on:click="toggle()">
            {{ $action }}
        </div>

        {{-- Teleported menu --}}
        <template x-teleport="body">
            <div x-ref="menu" x-show="open" x-transition x-cloak x-bind:style="style" x-on:click.stop
                class="fixed z-50 {{ $width }}
                   bg-gray-200 dark:bg-neutral-800 rounded-2xl
                    shadow-2xl border border-neutral-100 dark:border-white/5
                    transform scale-90 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] relative"
                style="display: none;">
                {{ $content }}
            </div>
        </template>
    </div>
</div>
