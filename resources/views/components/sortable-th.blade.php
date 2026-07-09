<th scope="col" {{ $attributes->merge(['class' => 'px-6 py-4 font-bold tracking-wider']) }}>
    @if ($sortable ?? false)
        <div class="flex items-center cursor-pointer group hover:text-indigo-600 transition-colors">
            {{ $slot }}
            <svg class="w-3 h-3 ms-1.5 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor"
                viewBox="0 0 24 24">
                <path
                    d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Z" />
            </svg>
        </div>
    @else
        {{ $slot }}
    @endif
</th>
