@props(['field', 'label' => null])

<x-core.th>
    <button type="button" x-on:click="sort('{{ $field }}')"
        class="flex items-center gap-1 uppercase font-medium select-none">
        {{ $label ?? $slot }}

        <span x-show="sortField === '{{ $field }}'" x-cloak class="inline-flex">
            <svg x-show="sortDirection === 'asc'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L10 5.414 6.707 8.707a1 1 0 01-1.414-1.414l4-4A1 1 0 0110 3z"
                    clip-rule="evenodd" />
            </svg>
            <svg x-show="sortDirection === 'desc'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 17a1 1 0 01-.707-.293l-4-4a1 1 0 111.414-1.414L10 14.586l3.293-3.293a1 1 0 111.414 1.414l-4 4A1 1 0 0110 17z"
                    clip-rule="evenodd" />
            </svg>
        </span>
    </button>
</x-core.th>
