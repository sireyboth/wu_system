{{-- resources/views/components/button.blade.php --}}

@props([
    'variant' => 'primary', // primary | secondary | info | success | warning | error | danger | ghost | outline
    'size' => 'md', // sm | md | lg
    'type' => 'button', // button | submit | reset
    'icon' => null, // e.g. 'fas-plus', 'fas-save', etc.
    'iconPosition' => 'left', // left | right
    'uppercase' => true,
    'rounded' => 'xl', // none | sm | md | lg | xl | 2xl | full
    'shadow' => true,
])

@php
    $base =
        'inline-flex items-center justify-center gap-1.5 font-bold transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100';

    $sizes = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-2.5 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];

    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        'full' => 'rounded-full',
    ];

    $variants = [
        'primary' => 'text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600',
        'secondary' =>
            'text-gray-600 bg-gray-400 hover:bg-gray-300 dark:text-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 shadow-md',
        'info' => 'text-white bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-500 shadow-lg',
        'success' =>
            'text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 shadow-lg',
        'warning' => 'text-white bg-amber-500 hover:bg-amber-600  dark:bg-amber-600 dark:hover:bg-amber-500 shadow-lg',
        'error' => 'text-white bg-rose-600 hover:bg-rose-700  dark:bg-rose-500 dark:hover:bg-rose-600 shadow-lg',
        'danger' => 'text-white bg-red-600 hover:bg-red-700  dark:bg-red-500 dark:hover:bg-red-600 shadow-lg',
        'ghost' => 'text-gray-700 bg-transparent hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
        'outline' =>
            'text-indigo-600 bg-transparent border-2 border-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:border-indigo-400 dark:hover:bg-indigo-950',
    ];

    $classes = collect([
        $base,
        $sizes[$size] ?? $sizes['md'],
        $roundedClasses[$rounded] ?? $roundedClasses['xl'],
        $variants[$variant] ?? $variants['primary'],
        $shadow ? '' : 'shadow-none',
        $uppercase ? 'uppercase' : '',
        $attributes->get('class'),
    ])
        ->filter()
        ->implode(' ');
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon && $iconPosition === 'left')
        <x-dynamic-component :component="$icon" class="h-4 w-4 shrink-0" />
    @endif

    @if ($slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif

    @if ($icon && $iconPosition === 'right')
        <x-dynamic-component :component="$icon" class="h-4 w-4 shrink-0" />
    @endif
</button>

{{-- Other variants --}}
{{-- <x-ui.button variant="success" icon="fas-check">Save</x-ui.button>
<x-ui.button variant="warning" icon="fas-exclamation-triangle">Warning</x-ui.button>
<x-ui.button variant="error" icon="fas-trash">Delete</x-ui.button>
<x-ui.button variant="info" icon="fas-info-circle">Info</x-ui.button>
<x-ui.button variant="secondary">Cancel</x-ui.button>
<x-ui.button variant="ghost">Ghost</x-ui.button>
<x-ui.button variant="outline">Outline</x-ui.button> --}}

{{-- Sizes --}}
{{-- <x-ui.button size="sm">Small</x-ui.button>
<x-ui.button size="lg" icon="fas-plus">Large</x-ui.button> --}}

{{-- Icon on the right --}}
{{-- <x-ui.button icon="fas-arrow-right" icon-position="right">Next</x-ui.button> --}}

{{-- Submit button --}}
{{-- <x-ui.button type="submit" variant="success" icon="fas-save">Save Changes</x-ui.button> --}}

{{-- Extra attributes --}}
{{-- <x-ui.button variant="primary" class="w-full" disabled>
    Disabled
</x-ui.button> --}}
