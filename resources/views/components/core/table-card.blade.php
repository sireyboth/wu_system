{{--
    Root of any resource index page. Owns the dataTable() Alpine instance,
    so every child (search input, sortable-th, filter dropdown, row-actions,
    pagination) can just reach up into this scope.

    Slots:
      toolbar  - buttons/dropdowns shown right of the search box (Add, Actions, Filter...)
      head     - <tr> contents for the <thead>
      default  - the <template x-for="row in rows"> row markup
--}}
@props([
    'endpoint',
    'sort' => null,
    'direction' => 'asc',
    'perPage' => 10,
    'noData' => 'No records found.',
    'showIndex' => true,
])


<div x-data="listTable({
    endpoint: '{{ $endpoint }}',
    sort: {{ $sort ? "'{$sort}'" : 'null' }},
    direction: '{{ $direction }}',
    perPage: {{ $perPage }},
})" class="bg-white dark:bg-neutral-900 relative shadow-md sm:rounded-lg overflow-hidden">
    {{-- toolbar --}}
    <div class=" flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
        <div class="w-full md:w-1/2">
            <x-ui.search-input />
        </div>

        <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
            {{ $toolbar ?? '' }}
        </div>
    </div>

    {{-- loading overlay --}}
    <div x-show="loading" x-transition.opacity
        class="absolute inset-0 z-20 flex items-center justify-center bg-white/60 dark:bg-gray-900/60"
        style="display: none;">
        <x-ui.loading-spinner />
    </div>

    {{-- table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full whitespace-nowrap text-sm text-left text-neutral-500 dark:text-neutral-400">
            <thead class="hidden md:table-header-group sticky top-0 z-20 text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-b border-neutral-200 dark:border-white/5">
                <tr>
                    @if ($showIndex)
                        <x-core.table-header label="#" />
                    @endif

                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-white/5">
                <template x-for="(row, index) in rows" :key="row.id">
                    <tr>
                        @if ($showIndex)
                            <x-core.table-data x-text="(meta.current_page - 1) * perPage + index + 1" />
                        @endif

                        {{ $slot }}
                    </tr>
                </template>

                <tr x-show="!loading && rows.length === 0" style="display: none;">
                    <td colspan="100%" class="px-4 py-6 text-center text-neutral-500 dark:text-neutral-400">
                        {{ $noData }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <x-core.pagination />
</div>
