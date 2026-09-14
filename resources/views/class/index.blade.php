@extends('layouts.dashboard')
@section('title', 'Classes')
@section('content')

    <x-core.page-header title="ថ្នាក់រៀន (Classes)" subtitle="Create classes, roster students automatically, and set each class's own scoring split." />

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
                <input id="classSearchInput" type="text"
                    class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                    placeholder="Search class code..." autocomplete="off" />
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="ClassModal.toggle(true)"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Class
                </button>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm transition-colors duration-300">
            <div id="loading-overlay"
                class="hidden absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div class="overflow-x-auto max-h-[600px] scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
                <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400 border-collapse">
                    <thead class="sticky top-0 z-20 text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">N.O</th>
                            <th scope="col" class="px-6 py-4">Code</th>
                            <th scope="col" class="px-6 py-4">Subject</th>
                            <th scope="col" class="px-6 py-4">Term</th>
                            <th scope="col" class="px-6 py-4">Lecturer</th>
                            <th scope="col" class="px-6 py-4 text-center">Enrolled</th>
                            <th scope="col" class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="class-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center">
                                <span class="text-neutral-500">Loading data...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('class.partials.classModal')
    @include('class.partials.autoEnrollModal')
    @include('class.partials.rosterModal')
    @include('class.partials.assignLecturerModal')
@endsection

@push('scripts')
    @vite(['resources/js/class/class.js'])
@endpush
