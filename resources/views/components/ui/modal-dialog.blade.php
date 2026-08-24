@props(['name', 'open' => false, 'size' => '2xl'])

@php
    $max_size = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
        'full' => 'sm:max-w-full',
    ][$size];
@endphp

<div x-data="modalPopup(@js($name), @js($open))" x-on:close.stop="close()" x-on:keydown.escape.window="close()"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" x-show="open" x-cloak class="fixed inset-0 z-50"
    style="display: {{ $open ? 'block' : 'none' }};">
    {{-- Backdrop --}}
    <div x-show="open" class="fixed inset-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="close()"></div>

    {{-- Centering Container --}}
    <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4">

        {{-- Modal Panel --}}
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
            class="relative w-full {{ $max_size }} bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 overflow-hidden"
            @click.stop>

            {{-- Loading Overlay (inside panel) --}}
            <div x-show="loading" x-transition
                class="absolute inset-0 z-20 flex items-center justify-center bg-white/80 dark:bg-neutral-900/80 backdrop-blur-[2px]">
                <x-spinner size="lg" style="info" />
            </div>

            {{-- Content --}}
            <div :class="{ 'opacity-50 pointer-events-none': loading }">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
