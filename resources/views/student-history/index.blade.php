@extends('layouts.dashboard')
@section('title', 'Student History')
@section('content')

    <x-core.page-header title="ប្រវត្តិសិក្សានិស្សិត (Student Academic History)" />

    <div class="space-y-4">
        <div class="relative w-full md:w-[28rem] group">
            <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                </svg>
            </div>
            <input id="historySearchInput" type="text" autocomplete="off"
                class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                placeholder="Search by student code or name..." />

            <div id="historySearchResults"
                class="hidden absolute z-30 mt-1.5 w-full max-h-80 overflow-y-auto bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-lg divide-y divide-neutral-100 dark:divide-white/5">
            </div>
        </div>

        <div id="historyEmptyState"
            class="flex flex-col items-center justify-center gap-3 py-20 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl text-center">
            <svg class="w-10 h-10 text-neutral-300 dark:text-neutral-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Search for a student above to see their full academic timeline.</p>
        </div>

        <div id="historyContent" class="hidden space-y-4">
            <div class="flex items-center gap-4 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm px-5 py-4">
                <div id="historyStudentInitial" class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white font-bold text-lg shrink-0">?</div>
                <div class="min-w-0">
                    <div id="historyStudentName" class="font-bold text-neutral-900 dark:text-white truncate">—</div>
                    <div id="historyStudentCode" class="text-xs text-neutral-400 font-mono">—</div>
                </div>
            </div>

            <div id="historyTimeline" class="relative pl-8 space-y-6"></div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/student-history/index.js'])
@endpush
