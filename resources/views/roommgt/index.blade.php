@extends('layouts.dashboard')
@section('content')
@include('roommgt.partials.add_modal')
    <!-- {{-- Page Header --}} -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
            សូមស្វាគមន៍មកកាន់ទំព័រ <span class="text-indigo-700">គ្រប់គ្រងបន្ទប់</span>
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

                <form id="roomSearchForm" method="GET" action="{{ route('roomsmgt.index') }}"
                    class="relative w-full md:w-96 group">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                        </svg>
                    </div>

                    <input id="roomSearchInput" type="text" name="search" value="{{ request('search') }}"
                        class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                        placeholder="Search room number, type, status..." autocomplete="off" />
                </form>

            </div>
            <!-- Button -->
            <div class="flex items-center gap-2">
                <!-- <button type="button" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-200 rounded-xl hover:bg-neutral-50 dark:bg-neutral-900 dark:text-neutral-300 dark:border-white/10 dark:hover:bg-white/5 transition-all">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2.586a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707V17l-4 4v-6.586a1 1 0 0 0-.293-.707L3.293 7.293A1 1 0 0 1 3 6.586V4Z"/></svg>
                    Filters
                </button> -->
                <button type="button" onclick="toggleModal()"
    class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    Add Room
</button>

            </div>
        </div>

        <div
            class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm transition-colors duration-300">

            <div id="loading-overlay"
                class="hidden absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div
                class="overflow-x-auto max-h-[600px] scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
                <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400 border-collapse">
                    <thead
                        class="sticky top-0 z-20 text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">
                                <div class="flex items-center cursor-pointer group hover:text-indigo-600 transition-colors">
                                    Room Type
                                    <svg class="w-3 h-3 ms-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Z" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">Room Number</th>
                            <th scope="col" class="px-6 py-4">Unit Price</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4">Note</th>
                            <th scope="col" class="px-6 py-4">Date Create</th>
                            <th scope="col" class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rooms-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                       <tr>
                            <td colspan="7" class="px-6 py-10 text-center">
                                <span class="text-neutral-500">Loading data...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="pagination-container" class="px-6 py-4 border-t border-neutral-200 dark:border-white">
            </div>

        </div>
    </div>
    </div>
    @endsection
    
@push('scripts')
  @vite(['resources/js/room-mgt/room-mgt.js'])
@endpush