{{--
    <x-core.table endpoint="/users">
        <x-slot:header>
            <x-table-head>Name</x-table-head>
            <x-table-head>Email</x-table-head>
        </x-slot:header>

        <x-slot:content>
            <x-table-cell x-text="row.name" />
            <x-table-cell x-text="row.email" />
        </x-slot:content>
    </x-core.table>

    Same tabledata() controller as x-core.table-card — this is just a
    cleaner-to-read slot naming, writing vendor <x-table-head>/<x-table-cell>
    directly instead of going through wrapper components.
--}}
@props([
    'endpoint',
    'sort' => null,
    'direction' => 'asc',
    'perPage' => 10,
    'noData' => 'No records found.',
    'showIndex' => true,
    'filterable' => true,
    'searchField' => null, // fixed field to always search, e.g. 'name'
    'searchFields' => null, // array to render a picker dropdown instead, e.g. ['name' => 'Name', 'email' => 'Email']
    'searchHint' => 'Search...',
])

<div x-data="tabledata({
    endpoint: '{{ $endpoint }}',
    sort: {{ $sort ? "'{$sort}'" : 'null' }},
    direction: '{{ $direction }}',
    perPage: {{ $perPage }},
    searchField: {{ $searchField ? "'{$searchField}'" : 'null' }},
})" class="bg-white dark:bg-neutral-900 relative shadow-xl sm:rounded-lg overflow-hidden">

    {{-- toolbar --}}
    @if ($filterable)
        <x-ui.toolbar :fields="$searchFields" :hint="$searchHint" />
    @endif

    {{-- table --}}
    <div class="overflow-x-auto">
        <x-table container:class="max-h-80" hover>
            <x-table-header class="uppercase font-semibold" sticky>
                <x-table-row>
                    @if ($showIndex)
                        <x-table-head sticky>#</x-table-head>
                    @endif

                    {{ $header }}
                </x-table-row>
            </x-table-header>

            <x-table-body highlight="even">
                {{-- Skeleton items — colspan="100%" means this works regardless of how
                     many <x-table-head> columns were actually passed in the header slot;
                     no column count needed. --}}
                <template x-if="loading">
                    <template x-for="i in 6" :key="i">
                        <x-table-row class="animate-pulse">
                            <x-table-cell colspan="100%" class="py-5">
                                <x-ui.skeleton />
                            </x-table-cell>
                        </x-table-row>
                    </template>
                </template>

                {{-- Rows --}}
                <template x-if="!loading && !error">
                    <template x-for="(item, index) in items" :key="item?.id ?? index">
                        <x-table-row x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            @if ($showIndex)
                                <x-table-head sticky x-text="(meta.current_page - 1) * perPage + index + 1" />
                            @endif

                            {{ $content }}
                        </x-table-row>
                    </template>
                </template>

                <x-table-row x-show="!loading && !error && items.length === 0" style="display: none;">
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
