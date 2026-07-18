@extends('layouts.dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
            សូមស្វាគមន៍មកកាន់ទំព័រ <span class="text-indigo-700">គ្រប់គ្រងសញ្ញាបត្របណ្ដោះអាសន្ន</span>
        </h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            Overview of your application
        </p>
    </div>

    <div class="space-y-4">

        {{-- Toolbar --}}
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">

            {{-- Search --}}
            <form id="studentSearchForm" method="GET" action="{{ route('student.index') }}"
                class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none ps-3">
                    <svg class="w-4 h-4 transition-colors text-neutral-500 group-focus-within:text-indigo-500"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                class="md:overflow-x-auto max-h-[600px]
                        scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
                <table
                    class="block w-full text-sm text-left text-neutral-500 dark:text-neutral-400 md:table md:border-collapse">
                    <thead
                        class="sticky top-0 z-20 hidden text-xs uppercase border-b md:table-header-group text-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-neutral-200 dark:border-white/5">
                        <tr>
                            <th class="w-12 px-6 py-4 font-bold tracking-wider">N.O</th>
                            <th class="px-6 py-4">Student Identity (ឈ្មោះ/អត្តសញ្ញាណ)</th>
                            <th class="px-6 py-4">Student ID (កូដសម្គាល់)</th>
                            <th class="px-6 py-4">Sex (ភេទ)</th>
                            <th class="px-6 py-4">Date of Birth (ថ្ងៃខែឆ្នាំកំណើត)</th>
                            <th class="px-6 py-4">Academic Plan (ជំនាញ/ជំនាន់)</th>
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
    @include('StudentStatusCertificate.partials.student-picker-modal')
    @include('StudentStatusCertificate.partials.student-status-modal')
@endsection

@push('scripts')
    @vite(['resources/js/StudentStatusCertificate/index.js'])
@endpush
