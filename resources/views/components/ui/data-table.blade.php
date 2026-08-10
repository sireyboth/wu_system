{{--
    Reusable admin table shell: loading overlay, responsive thead (hidden on
    mobile since table-render.js renders a card layout below md:), and an
    empty tbody the page's JS module fills in via innerHTML.

    Usage:
        <x-ui.data-table
            :headers="['N.O', 'Room', 'Major', ['label' => 'Actions', 'align' => 'right']]"
            body-id="state-exam-table-body"
        />

    Only the static shell lives here — row rendering stays in table-render.js,
    exactly as before. This component does NOT touch that.
--}}
@props([
    'headers' => [],
    'bodyId' => 'table-body',
    'colspan' => null,
    'loadingText' => 'Loading data...',
])

@php
    $colspan ??= count($headers) ?: 1;
@endphp

<div class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm transition-colors duration-300">

    <div id="loading-overlay"
        class="hidden absolute inset-0 z-10 items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
        <div class="w-10 h-10 border-b-2 border-indigo-600 rounded-full animate-spin"></div>
    </div>

    <div class="overflow-y-auto md:overflow-x-auto max-h-[600px] scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
        <table class="block w-full text-sm text-left text-neutral-500 dark:text-neutral-400 md:table md:border-collapse">
            <thead class="sticky top-0 z-20 hidden text-xs uppercase border-b md:table-header-group text-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-neutral-200 dark:border-white/5">
                <tr>
                    @foreach ($headers as $i => $header)
                        @php
                            $label = is_array($header) ? ($header['label'] ?? '') : $header;
                            $align = is_array($header) ? ($header['align'] ?? 'left') : 'left';
                        @endphp
                        <th scope="col"
                            class="px-6 py-4 {{ $i === 0 ? 'font-bold tracking-wider' : '' }} {{ $align === 'right' ? 'text-right' : '' }}">
                            {{ $label }}
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
