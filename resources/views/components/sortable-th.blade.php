@props(['field', 'sortable' => false, 'align' => 'left'])

<th class="px-4 py-3 text-{{ $align }}">
    @if ($sortable)
        <button
            type="button"
            x-on:click="sortBy('{{ $field }}')"
            class="inline-flex items-center gap-1 hover:text-neutral-200 transition-colors"
        >
            <span>{{ $slot }}</span>
            <span class="flex flex-col leading-none text-[10px]">
                <span :class="sortField === '{{ $field }}' && sortDir === 'asc' ? 'text-indigo-400' : 'text-neutral-600'">&#9650;</span>
                <span :class="sortField === '{{ $field }}' && sortDir === 'desc' ? 'text-indigo-400' : 'text-neutral-600'">&#9660;</span>
            </span>
        </button>
    @else
        {{ $slot }}
    @endif
</th>
