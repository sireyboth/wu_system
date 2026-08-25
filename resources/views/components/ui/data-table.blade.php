{{--
    Reusable admin table shell: loading overlay, responsive thead (hidden on
    mobile since table-render.js renders a card layout below md:), an empty
    tbody the page's JS module fills in via innerHTML, and an optional
    select-all + bulk-delete bar.

    Usage:
        <x-ui.data-table
            :headers="['N.O', 'Room', 'Major', ['label' => 'Actions', 'align' => 'right']]"
            body-id="state-exam-table-body"
            selectable
        />

    When `selectable` is on, each row's JS template is expected to render a
    checkbox with class="row-select" data-id="{id}" as its first cell (see
    table-render.js). This component only owns the select-all checkbox and
    the floating bulk-delete bar's markup — wiring them up (tracking which
    rows are checked, firing the bulk-delete request) is plain JS, not this
    component's job. It exposes stable IDs for that JS to hook into:
        #{bodyId}-select-all      the header checkbox
        #{bodyId}-bulk-bar        the bar (hidden until something's selected)
        #{bodyId}-bulk-count      text showing how many are selected
        #{bodyId}-bulk-delete-btn the delete button

    Only the static shell lives here — row rendering stays in table-render.js,
    exactly as before. This component does NOT touch that.
--}}
@props([
    'headers' => [],
    'bodyId' => 'table-body',
    'colspan' => null,
    'loadingText' => 'Loading data...',
    'selectable' => false,
])

@php
    $colspan ??= count($headers) + ($selectable ? 1 : 0) ?: 1;
@endphp

<div class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm transition-colors duration-300">

    <div id="loading-overlay"
        class="hidden absolute inset-0 z-10 items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
        <div class="w-10 h-10 border-b-2 border-indigo-600 rounded-full animate-spin"></div>
    </div>

    @if ($selectable)
        <div id="{{ $bodyId }}-bulk-bar"
            class="hidden items-center justify-between gap-3 px-6 py-3 bg-indigo-50 dark:bg-indigo-500/10 border-b border-indigo-100 dark:border-indigo-500/20">
            <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                <span id="{{ $bodyId }}-bulk-count">0</span> ជ្រើសរើស (selected)
            </span>
            <button type="button" id="{{ $bodyId }}-bulk-delete-btn"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-rose-600 rounded-lg shadow-sm hover:bg-rose-700 active:scale-95 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                លុបជាបណ្តុំ (Delete Selected)
            </button>
        </div>
    @endif

    <div class="overflow-y-auto md:overflow-x-auto max-h-[600px] scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
        <table class="block w-full text-sm text-left text-neutral-500 dark:text-neutral-400 md:table md:border-collapse">
            <thead class="sticky top-0 z-20 hidden text-xs uppercase border-b md:table-header-group text-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-neutral-200 dark:border-white/5">
                <tr>
                    @if ($selectable)
                        <th scope="col" class="w-10 px-6 py-4">
                            <input type="checkbox" id="{{ $bodyId }}-select-all"
                                class="w-4 h-4 rounded border-neutral-300 dark:border-white/20 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer">
                        </th>
                    @endif
                    @foreach ($headers as $i => $header)
                        @php
                            $label = is_array($header) ? ($header['label'] ?? '') : $header;
                            $align = is_array($header) ? ($header['align'] ?? 'left') : 'left';
                            $sortKey = is_array($header) ? ($header['sort'] ?? null) : null;
                        @endphp
                        <th scope="col"
                            class="px-6 py-4 {{ $i === 0 && !$selectable ? 'font-bold tracking-wider' : '' }} {{ $align === 'right' ? 'text-right' : '' }}">
                            @if ($sortKey)
                                <button type="button" data-sort-key="{{ $sortKey }}" data-sort-table="{{ $bodyId }}"
                                    class="sortable-th inline-flex items-center gap-1 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $label }}
                                    <svg class="sort-icon w-3.5 h-3.5 text-neutral-300 dark:text-neutral-600 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            @else
                                {{ $label }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="{{ $bodyId }}" class="divide-y divide-neutral-200 dark:divide-white/5">
                <tr>
                    <td colspan="{{ $colspan }}" class="px-6 py-10 text-center">
                        <span class="text-neutral-500">{{ $loadingText }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="pagination-container" class="px-6 py-4 border-t border-neutral-200 dark:border-white/5"></div>
</div>
