{{--
    Root of any resource index page. Owns the listTable() Alpine instance,
    so every child (search input, sortable-th, row-actions, pagination)
    just reaches up into this scope — no props passed between them.

    Slots:
      toolbar  - buttons/dropdowns shown right of the search box (Add, Filter...)
      head     - <tr> contents for the <thead>
      default  - <td> contents per row (x-text="row.field" etc.), rendered
                 once and cloned per row by the x-for below
--}}
@props([
    'endpoint',
    'sort' => null,
    'direction' => 'asc',
    'perPage' => 10,
    'noData' => 'No records found.',
    'showIndex' => true,
    'filterable' => true,
])
<div x-data="table({
    endpoint: '{{ $endpoint }}',
    sort: {{ $sort ? "'{$sort}'" : 'null' }},
    direction: '{{ $direction }}',
    perPage: {{ $perPage }},
})" class="bg-white dark:bg-neutral-900 relative shadow-md sm:rounded-lg overflow-hidden">

    {{-- toolbar --}}
    @if ($filterable)
        <x-ui.toolbar />
    @endif

    {{-- table --}}
    <div class="overflow-x-auto">
        <x-table hover>
            <x-table-header class="uppercase font-semibold">
                <x-table-row>
                    @if ($showIndex)
                        <x-table-head>#</x-table-head>
                    @endif

                    {{ $header }}
                </x-table-row>
            </x-table-header>

            <x-table-body highlight="even">
                {{-- Skeleton rows — colspan="100%" means this works regardless of how
                     many <x-table-head> columns were actually passed in the header slot;
                     no column count needed. --}}
                <template x-if="loading">
                    <template x-for="i in 6" :key="i">
                        <x-table-row class="animate-pulse" >
                            <x-table-cell colspan="100%" class="py-5">
                                <x-ui.skeleton />
                            </x-table-cell>
                        </x-table-row>
                    </template>
                </template>

                {{-- Rows --}}
                <template x-if="!loading && !error">
                    <template x-for="(row, index) in rows" :key="row.id">
                        <x-table-row x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            @if ($showIndex)
                                <x-table-head x-text="(meta.current_page - 1) * perPage + index + 1" />
                            @endif

                            {{ $content }}
                        </x-table-row>
                    </template>
                </template>

                <x-table-row x-show="!loading && !error && rows.length === 0" style="display: none;">
                    <x-table-cell colspan="100%" align="center">{{ $noData }}</x-table-cell>
                </x-table-row>

                <x-table-row x-show="!loading && error" style="display: none;">
                    <x-table-cell colspan="100%" align="center" class="text-red-500" x-text="error" />
                </x-table-row>
            </x-table-body>
        </x-table>
    </div>

    <x-ui.pagination />
</div>
