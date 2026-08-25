@extends('layouts.dashboard')
@section('title', 'Alerts & Reminders')
@section('content')

    <x-core.page-header title="ការជូនដំណឹង និងការរំលឹក" subtitle="Alerts & Reminders — track deadlines and get nagged on Telegram until they're done" />

    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none z-10">
                    <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
                <input id="alertSearchInput" type="text"
                    class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                    placeholder="Search title, category, content..." autocomplete="off" />
            </div>

            <button type="button" id="createAlertBtn" onclick="AlertModal.open()"
                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 rounded-xl shadow-md shadow-indigo-500/25 hover:shadow-lg hover:shadow-indigo-500/35 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 active:scale-95 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>New Alert</span>
            </button>
        </div>

        <div id="loading-overlay" class="hidden fixed inset-0 z-10 items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
            <div class="w-10 h-10 border-b-2 border-indigo-600 rounded-full animate-spin"></div>
        </div>

        <!-- Dashboard view (no search) -->
        <div id="boardView" class="space-y-8">
            <section>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <h2 class="text-sm font-bold uppercase tracking-widest text-rose-600 dark:text-rose-400">ខ្ពស់ (High) — due within 1 day / overdue</h2>
                </div>
                <div id="highSection" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"></div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <h2 class="text-sm font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">មធ្យម (Medium) — due in 2-3 days</h2>
                </div>
                <div id="mediumSection" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"></div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-neutral-400"></span>
                    <h2 class="text-sm font-bold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">ក្រោយៗទៀត (Later) — more than 3 days out</h2>
                </div>
                <div id="laterSection" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"></div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">រួចរាល់ (Done)</h2>
                </div>
                <x-ui.data-table
                    :headers="['ចំណងជើង (Title)', 'ប្រភេទ (Category)', 'ធ្វើរួចនៅ (Completed)', ['label' => 'សកម្មភាព (Actions)', 'align' => 'right']]"
                    body-id="alert-done-table-body" />
            </section>
        </div>

        <!-- Search results view -->
        <div id="searchView" class="hidden">
            <div class="flex items-center gap-2 mb-3">
                <h2 class="text-sm font-bold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">លទ្ធផលស្វែងរក (Search results)</h2>
            </div>
            <div id="searchSection" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"></div>
        </div>
    </div>

    @include('alerts.partials.alertModal')

@endsection

@push('scripts')
    @vite(['resources/js/alerts/index.js'])
@endpush
