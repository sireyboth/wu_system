@extends('layouts.dashboard')
@section('content')

    <!-- {{-- Page Header --}} -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
            សូមស្វាគមន៍មកកាន់ទំព័រ <span class="text-indigo-700">គ្រប់គ្រងការប្រឡងបញ្ចប់ការសិក្សា</span>
        </h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            Overview of your application
        </p>
    </div>

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

                <form id="state-examSearchForm" method="GET" action="{{ route('stateExam.index') }}"
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
            <div class="flex items-center gap-2">
                <!-- <button type="button" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-200 rounded-xl hover:bg-neutral-50 dark:bg-neutral-900 dark:text-neutral-300 dark:border-white/10 dark:hover:bg-white/5 transition-all">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2.586a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707V17l-4 4v-6.586a1 1 0 0 0-.293-.707L3.293 7.293A1 1 0 0 1 3 6.586V4Z"/></svg>
                    Filters
                </button> -->
                <button type="button" onclick="AppModal.toggle(true)"
                class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Create New state-exam
            </button>

            </div>
        </div>

        <div
            class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm transition-colors duration-300">

            <div id="loading-overlay"
                class="hidden absolute inset-0 z-10 items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div
                class="overflow-y-auto md:overflow-x-auto max-h-[600px] scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
                <table class="block w-full text-sm text-left text-neutral-500 dark:text-neutral-400 md:table md:border-collapse">
                    <thead
                        class="sticky top-0 z-20 hidden text-xs uppercase border-b md:table-header-group text-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-neutral-200 dark:border-white/5">
                        <tr>
                            <th scope="col" class="w-12 px-6 py-4 font-bold tracking-wider">N.O</th>
                            <th scope="col" class="px-6 py-4">Room</th>
                            <th scope="col" class="px-6 py-4">Major</th>
                            <th scope="col" class="px-6 py-4">Degree</th>
                            <th scope="col" class="px-6 py-4">Shift</th>
                            <th scope="col" class="px-6 py-4">Students</th>
                            <th scope="col" class="px-6 py-4">Exam Date</th>
                            <th scope="col" class="px-6 py-4">Attendance</th>
                            <th scope="col" class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="state-exam-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                       <tr>
                            <td colspan="9" class="px-6 py-10 text-center">
                                <span class="text-neutral-500">Loading data...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="pagination-container" class="px-6 py-4 border-t border-neutral-200 dark:border-white/5">
            </div>

        </div>
    </div>

    @include('stateExam.partials.stateExamModal')
    @endsection

@push('scripts')
  @vite(['resources/js/stateExam/index.js'])

@endpush
