<x-sidebar-link :route="$route">
    <x-slot name="icon">
        <x-icon :name="$icon" />
    </x-slot>
    {{ $label }}
</x-sidebar-link>
