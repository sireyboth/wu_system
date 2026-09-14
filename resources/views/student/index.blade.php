@extends('layouts.dashboard')
@section('title', 'Students')
@section('content')
    <x-core.page-header title="គ្រប់គ្រងនិស្សិត" />
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

                <form id="studentSearchForm" method="GET" action="{{ route('student.index') }}"
                    class="relative w-full md:w-96 group">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                        </svg>
                    </div>

                    <input id="studentSearchInput" type="text" name="search" value="{{ request('search') }}"
                        class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                        placeholder="Search Invoice number, type, status..." autocomplete="off" />
                </form>

            </div>
            <!-- Button -->
            <div class="flex items-center gap-2">
                <button type="button" id="studentImportBtn"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                    <svg class="w-4 h-4 mr-2 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    នាំចូល (Import)
                </button>
                <button type="button" id="studentExportBtn" title="Export the current filtered list to Excel"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                    <svg class="w-4 h-4 mr-2 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    នាំចេញ (Export)
                </button>
                <button type="button" onclick="AppModal.toggle(true)"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create New student
                </button>
                {{-- Temporary — hide/remove once done testing (bulk hard-delete). --}}
                @can('student.delete')
                <button type="button" id="studentDestroyAllBtn" title="Permanently delete ALL students — cannot be undone"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    លុបទាំងអស់ (Destroy All)
                </button>
                @endcan

            </div>
        </div>

        <div class="flex flex-wrap items-end gap-3 p-4 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl">
            <div class="min-w-[150px]">
                <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Major</label>
                <select id="filter_major_id" class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">All Majors</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Batch</label>
                <select id="filter_batch_id" class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">All Batches</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Shift</label>
                <select id="filter_shift_id" class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">All Shifts</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Group</label>
                <select id="filter_group_id" class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">All Groups</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Campus</label>
                <select id="filter_campus_id" class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">All Campuses</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Status</label>
                <select id="filter_status_id" class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">All Statuses</option>
                </select>
            </div>
            <button type="button" id="filterClearBtn"
                class="px-3 py-2 text-xs font-semibold text-neutral-500 hover:text-neutral-800 dark:hover:text-white transition-colors">
                Clear filters
            </button>
        </div>

        <div id="bulkActionBar" class="hidden items-center gap-3 px-4 py-3 bg-teal-50 dark:bg-teal-500/10 border border-teal-200 dark:border-teal-500/20 rounded-2xl">
            <span id="bulkSelectedCount" class="text-sm font-bold text-teal-700 dark:text-teal-300">0 selected</span>
            <button type="button" id="bulkSelectAllFilteredBtn" class="text-xs font-medium text-teal-700 dark:text-teal-300 underline hover:no-underline"></button>
            <button type="button" id="bulkAdvanceSemesterBtn"
                class="ml-auto inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-teal-600 rounded-xl hover:bg-teal-700 shadow-md shadow-teal-500/20 transition-all active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5M9 15.75l2.25 2.25L15 13.5"/>
                </svg>
                Advance Selected to New Semester
            </button>
        </div>

        <div
            class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm transition-colors duration-300">

            <div id="loading-overlay"
                class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div
                class="overflow-y-auto md:overflow-x-auto max-h-[600px] scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
                <table
                    class="w-full whitespace-nowrap text-sm text-left text-neutral-500 dark:text-neutral-400 block md:table md:border-collapse">
                    <thead
                        class="hidden md:table-header-group sticky top-0 z-20 text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-b border-neutral-200 dark:border-white/5">
                        <tr>
                            <th scope="col" class="px-4 py-4 w-10">
                                <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-neutral-300 dark:border-white/20 text-teal-600 focus:ring-2 focus:ring-teal-500/30">
                            </th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider w-12">N.O</th>
                            <th scope="col" class="px-6 py-4">Student Identity (ឈ្មោះ/អត្តសញ្ញាណ)</th>
                            <th scope="col" class="px-6 py-4">Student ID (កូដសម្គាល់)</th>
                            <th scope="col" class="px-6 py-4">Sex (ភេទ)</th>
                            <th scope="col" class="px-6 py-4">Date of Birth (ថ្ងៃខែឆ្នាំកំណើត)</th>
                            <th scope="col" class="px-6 py-4">Academic Plan (ជំនាញ/ជំនាន់)</th>
                            <th scope="col" class="px-6 py-4">Status (ស្ថានភាព)</th>
                            <th scope="col" class="px-6 py-4">Official Date (កាលបរិច្ឆេទ)</th>
                            <th scope="col" class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center">
                                <span class="text-neutral-500">Loading student registry records...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="pagination-container" class="px-6 py-4 border-t border-neutral-200 dark:border-white/5"></div>

        </div>
    </div>
    </div>

    @include('student.partials.studentModal')
    @include('student.partials.preview')
    @include('student.partials.importModal')
    @include('student.partials.advanceSemesterModal')
    @include('student.partials.bulkAdvanceSemesterModal')
@endsection

@push('scripts')
    @vite(['resources/js/student/index.js'])
    {{-- @vite(['resources/js/student/preview.js']) --}}
@endpush
