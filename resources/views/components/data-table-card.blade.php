@php
    $searchPlaceholder = $searchPlaceholder ?? 'Search ' . strtolower($columns[0]['label'] ?? '') . '...';
@endphp

<div class="space-y-4">
    {{-- Toolbar: search + create button --}}
    <div class="flex items-center justify-between gap-4 mb-2">
        @if ($searchable)
            <x-search-input x-model="search" :placeholder="$searchPlaceholder" />
        @else
            <div></div>
        @endif

        @if ($creatable)
            <x-text-icon-button icon="plus" x-on:click="$dispatch('open-modal', 'modal-dialog')" />
        @endif
    </div>

    <div x-data="dataTable({
        endpoint: @js($endpoint),
        defaultSort: @js($defaultSort),
        defaultDir: @js($defaultDir),
        perPage: {{ $perPage }},
    })">
        {{-- Table --}}
        <div class="relative overflow-x-auto">
            <div x-show="loading" x-transition.opacity
                class="absolute inset-0 z-10 grid place-items-center bg-neutral-900/60">
                <x-loading-spinner />
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-800 text-xs uppercase tracking-wide text-neutral-400">
                        <th class="px-4 py-3 text-left">#</th>
                        @foreach ($columns as $col)
                            <x-sortable-th :field="$col['key']" :sortable="$col['sortable']" :align="$col['align']">
                                {{ $col['label'] }}
                            </x-sortable-th>
                        @endforeach
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <template x-if="!loading && items.length === 0">
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="px-4 py-10 text-center text-neutral-500">
                                No results found.
                            </td>
                        </tr>
                    </template>

                    <template x-for="(item, index) in items" :key="item.id">
                        <tr class="border-b border-neutral-800/60 hover:bg-neutral-800/40 transition-colors">
                            <td class="px-4 py-3 text-neutral-400" x-text="(page - 1) * perPage + index + 1"></td>

                            {{ $slot }}
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" x-on:click="$dispatch('edit-item', item)"
                                        class="text-orange-400 hover:text-orange-300">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button type="button" x-on:click="confirmDelete(item)"
                                        class="text-red-500 hover:text-red-400">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-neutral-800">
            <x-pagination />
        </div>

        {{-- Delete confirmation modal --}}
        <x-confirm-modal name="delete-confirm" title="Delete this item?" body="This action cannot be undone."
            x-on:confirm="deleteRow" />
    </div>
</div>
