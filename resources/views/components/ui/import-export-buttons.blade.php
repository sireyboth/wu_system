{{--
    Shared Import/Export button pair for an index page toolbar. Renders the
    exact markup that used to be hand-copied into faculty/group/subject/
    shift/student/status/major/lecturer's index.blade.php — same classes,
    same icons, same ids (`{prefix}ImportBtn` / `{prefix}ExportBtn`) so the
    existing per-page JS (which wires up click handlers by those ids) needs
    no changes. Changing button color/style here now updates every page
    that uses this component at once, instead of hand-editing N files.

    Usage:
        <x-ui.import-export-buttons prefix="faculty" />
--}}
@props([
    'prefix',
    'importLabel' => 'នាំចូល (Import)',
    'exportLabel' => 'នាំចេញ (Export)',
    'exportTitle' => 'Export the current filtered list to Excel',
])

<button type="button" id="{{ $prefix }}ImportBtn"
    class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-sky-700 dark:text-sky-400 bg-sky-50/80 dark:bg-sky-950/30 border border-sky-200/80 dark:border-sky-900/50 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-900/50 hover:border-sky-300 dark:hover:border-sky-800 transition-colors">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
    </svg>
    {{ $importLabel }}
</button>
<button type="button" id="{{ $prefix }}ExportBtn" title="{{ $exportTitle }}"
    class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-900/50 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-300 dark:hover:border-emerald-800 transition-colors">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
    </svg>
    {{ $exportLabel }}
</button>
