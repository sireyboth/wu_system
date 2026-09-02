@extends('layouts.dashboard')
@section('title', 'Provisional Certificates')

@section('content')
    <x-core.page-header title="គ្រប់គ្រងសញ្ញាបត្របណ្ដោះអាសន្ន" />

    <div class="space-y-4">

        {{-- Toolbar --}}
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">

            {{-- Search --}}
            <form id="studentSearchForm" method="GET" action="{{ route('student.index') }}"
                class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none ps-3">
                    <svg class="w-4 h-4 transition-colors text-neutral-500 group-focus-within:text-indigo-500" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
                <input id="studentSearchInput" type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search student name, ID, status…" autocomplete="off"
                    class="block w-full p-2.5 ps-10 text-sm text-neutral-900
                           border border-neutral-200 rounded-xl bg-white
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                           dark:bg-neutral-900 dark:border-white/10
                           dark:placeholder-neutral-400 dark:text-white transition-all" />
            </form>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button type="button" id="openReportBtn" aria-label="Open Report"
                    class="group relative inline-flex items-center justify-center gap-2.5 px-5 py-3 text-sm font-semibold tracking-wide text-neutral-800 dark:text-neutral-100 bg-neutral-900/5 dark:bg-neutral-100/10 backdrop-blur-md rounded-2xl border border-neutral-300/60 dark:border-white/15 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 dark:hover:shadow-indigo-400/20 hover:border-indigo-500/50 dark:hover:border-indigo-400/50 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.97] transition-all duration-300 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-950 overflow-hidden">

                    <!-- Ambient Animated Glow Background -->
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 via-purple-500/20 to-pink-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 blur-xl pointer-events-none"></span>

                    <!-- Subtle Shimmer Sweep Overlay -->
                    <span
                        class="absolute inset-0 -translate-x-full group-hover:translate-x-full bg-gradient-to-r from-transparent via-white/20 dark:via-white/10 to-transparent transition-transform duration-1000 ease-in-out pointer-events-none"></span>

                    <!-- Icon Container with Animated Hover Dynamics -->
                    <span
                        class="relative flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:rotate-[-6deg] text-indigo-600 dark:text-indigo-400">
                        <!-- Default State: Report Chart Icon -->
                        <svg id="btnIcon" class="w-4 h-4 transition-all duration-300" fill="none" stroke="currentColor"
                            stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-6h6v6m-9 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>

                        <!-- Loading State: Spinner (Hidden by default) -->
                        <svg id="btnSpinner" class="hidden w-4 h-4 animate-spin text-indigo-600 dark:text-indigo-400"
                            fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>

                    <!-- Button Text Label -->
                    <span id="btnText"
                        class="relative z-10 font-bold transition-colors duration-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-300">
                        Generate Report
                    </span>
                </button>
                <button type="button" id="openPickerBtn"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white
                           bg-indigo-600 rounded-xl hover:bg-indigo-700
                           shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create New Certificate
                </button>
            </div>

        </div>

        {{-- Table card --}}
        <div
            class="relative overflow-hidden transition-colors duration-300 bg-white border shadow-sm dark:bg-neutral-900 border-neutral-200 dark:border-white/10 rounded-2xl">

            <div id="loading-overlay"
                class="hidden absolute inset-0 z-10 items-center justify-center
                        bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
                <div class="w-10 h-10 border-b-2 border-indigo-600 rounded-full animate-spin"></div>
            </div>

            <div
                class="overflow-y-auto md:overflow-x-auto max-h-[600px]
                        scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
                <table
                    class="block w-full text-sm text-left whitespace-nowrap text-neutral-500 dark:text-neutral-400 md:table md:border-collapse">
                    <thead
                        class="sticky top-0 z-20 hidden text-xs uppercase border-b md:table-header-group text-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-neutral-200 dark:border-white/5">
                        <tr>
                            <th class="w-12 px-6 py-4 font-bold tracking-wider">N.O</th>
                            <th class="px-6 py-4">Student Identity (ឈ្មោះ/អត្តសញ្ញាណ)</th>
                            <th class="px-6 py-4">Student ID (កូដសម្គាល់)</th>
                            <th class="px-6 py-4">Sex (ភេទ)</th>
                            <th class="px-6 py-4">Date of Birth (ថ្ងៃខែឆ្នាំកំណើត)</th>
                            <th class="px-6 py-4">Academic Plan (ជំនាញ/ជំនាន់)</th>
                            <th class="px-6 py-4">Status-Acade (ស្ថានភាពសិក្សា)</th>
                            <th class="px-6 py-4">Status (ស្ថានភាព)</th>
                            <th class="px-6 py-4">Official Date (កាលបរិច្ឆេទ)</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center">
                                <span class="text-neutral-500">Loading student registry records…</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="pagination-container" class="px-6 py-4 border-t border-neutral-200 dark:border-white/5"></div>

        </div>
    </div>

    {{-- Student picker modal --}}
    @include('certificate.partials.student-picker-modal')
    @include('certificate.partials.student-status-modal')
    @include('certificate.partials.certificate-print')
    @include('certificate.partials.certificate-report-modal')
@endsection

@push('scripts')
    @vite(['resources/js/certificate/index.js'])
@endpush
