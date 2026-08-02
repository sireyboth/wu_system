{{--
    Generic dropdown. Pass the trigger element via the "trigger" slot and the
    panel contents via the default slot. Reused for the top Actions menu, the
    brand Filter menu, and each row's Show/Edit/Delete menu.

    The panel is teleported to <body> and positioned with fixed coordinates
    computed from the trigger's own position. This is what keeps it fully
    visible for rows near the bottom (or right edge) of .table-scroll --
    without teleporting, the panel is clipped by that container, because
    setting overflow-x:auto on an element forces overflow-y:auto too, so it
    clips both axes even though we only wanted horizontal scrolling.

    Alpine scope (e.g. "row" from an ancestor x-for) is still reachable
    inside the teleported content -- x-teleport keeps the original scope
    chain, it only moves *where in the DOM* the markup renders.

    NOTE: pass a full utility class to "width" (e.g. "w-44"), not a bare
    number — Tailwind can't detect dynamically-built class strings at build time.
--}}
@props([
    'align' => 'right', // left | right
    'width' => 'w-44',
])

<div x-data="{
    open: false,
    placement: { top: 0, left: 0, right: 0 },
    toggle() {
        if (this.open) {
            this.open = false;
            return;
        }

        const rect = this.$refs.trigger.getBoundingClientRect();
        this.placement = {
            top: rect.bottom + 8,
            left: rect.left,
            right: window.innerWidth - rect.right,
        };
        this.open = true;
    },
}" x-on:click.outside="open = false" x-on:scroll.window="open = false"
    x-on:resize.window="open = false" class="relative inline-block text-left">
    <div x-ref="trigger" x-on:click="toggle()">
        {{ $trigger }}
    </div>

    <template x-teleport="body">
        <div x-show="open" x-transition x-on:click="open = false" x-cloak
            x-bind:style="`top: ${placement.top}px; {{ $align === 'right' ? 'right' : 'left' }}: ${placement.{{ $align === 'right' ? 'right' : 'left' }}}px;`"
            class="fixed z-50 {{ $width }} bg-white rounded divide-y divide-gray-100 shadow-lg dark:bg-gray-700 dark:divide-gray-600"
            style="display: none;">
            {{ $slot }}
        </div>
    </template>
</div>
