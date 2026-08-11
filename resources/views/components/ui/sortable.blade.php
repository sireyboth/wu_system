@props(['field', 'label'])

<x-table-head {{ $attributes->class('px-4 py-3 font-medium cursor-pointer select-none') }}
    @click="sortBy('{{ $field }}')">
    <span class="inline-flex items-center gap-x-1">
        {{ $label }}
        <template x-if="sort === '{{ $field }}'">
            <span x-text="direction === 'asc' ? '↑' : '↓'"></span>
        </template>
    </span>
</x-table-head>
