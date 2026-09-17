@extends('layouts.dashboard')
@section('title', 'Exam Rooms')
@section('content')

    <x-core.page-header title="គ្រប់គ្រងការប្រឡងបញ្ចប់ការសិក្សា" />

    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>

                <form id="state-examSearchForm" method="GET" action="{{ route('state-exam.index') }}"
                    class="relative w-full md:w-96 group">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                        </svg>
                    </div>

                    <input id="state-examSearchInput" type="text" name="search" value="{{ request('search') }}"
                        class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                        placeholder="Search by room, major, degree, shift..." autocomplete="off" />
                </form>

            </div>
            <!-- Button -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Exam Term filter + create -->
                <div class="flex items-center gap-1.5">
                    <select id="examTermFilterSelect" class="text-sm rounded-xl border-neutral-200 dark:border-white/10 bg-white dark:bg-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">គ្រប់រយៈពេល (All terms)</option>
                    </select>
                    <button type="button" id="newExamTermBtn" title="Create a new exam term"
                        class="shrink-0 p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>

                <!-- Trash / Recycled Items Toggle -->
                <button type="button" id="toggleTrashBtn"
                    class="group relative inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-rose-700 dark:text-rose-400 bg-rose-50/80 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/50 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/50 hover:border-rose-300 dark:hover:border-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 active:scale-95 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-rose-600 dark:text-rose-400 transition-transform duration-200 group-hover:rotate-12"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span id="toggleTrashLabel">ធុងសំរាម (Trash)</span>
                </button>

                <!-- Export / Import -->
                <button type="button" id="examStatesExportBtn" title="Export the rooms currently shown to Excel"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-900/50 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-300 dark:hover:border-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 active:scale-95 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export</span>
                </button>

                <button type="button" id="examStatesImportBtn" title="Bulk create/fix rooms for the selected term"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-sky-700 dark:text-sky-400 bg-sky-50/80 dark:bg-sky-950/30 border border-sky-200/80 dark:border-sky-900/50 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-900/50 hover:border-sky-300 dark:hover:border-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 active:scale-95 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 7.5L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <span>Import</span>
                </button>
                <input type="file" id="examStatesImportFileInput" accept=".xlsx,.xls,.csv" class="hidden" />

                <!-- Analytics Report Link -->
                <a href="{{ route('state-exam.report') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400/20 active:scale-95 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    <span>របាយការណ៍ (Report)</span>
                </a>

                <!-- Primary Action: Create New State Exam -->
                <button type="button" id="createRoomBtn" onclick="AppModal.toggle(true)"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 rounded-xl shadow-md shadow-indigo-500/25 hover:shadow-lg hover:shadow-indigo-500/35 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 active:scale-95 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Create New State Exam</span>
                </button>
            </div>
        </div>

        <x-ui.data-table :headers="[
            'N.O',
            ['label' => 'Room', 'sort' => 'room'],
            'Major',
            'Degree',
            'Shift',
            'Students',
            'Exam Date',
            'Attendance',
            ['label' => 'Actions', 'align' => 'right'],
        ]" body-id="state-exam-table-body" selectable />
    </div>

    @include('state-exam.partials.stateExamModal')
    @include('state-exam.partials.examTermModal')
@endsection

@push('scripts')
    @vite(['resources/js/state-exam/index.js'])
@endpush
