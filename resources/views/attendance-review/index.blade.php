@extends('layouts.dashboard')
@section('title', 'Attendance Review')
@section('content')

    <x-core.page-header title="ត្រួតពិនិត្យវត្តមាន (Attendance Review)" subtitle="Flagged scans and correction requests — nothing here changes a record until you decide it." />

    <div class="space-y-8">
        <!-- Flagged scans -->
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-neutral-900 dark:text-white">Flagged Scans</h2>
                <span id="flaggedCount" class="text-xs font-semibold text-neutral-400"></span>
            </div>
            <div class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400 border-collapse">
                        <thead class="text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300">
                            <tr>
                                <th class="px-6 py-3">Student</th>
                                <th class="px-6 py-3">Class</th>
                                <th class="px-6 py-3">Risk</th>
                                <th class="px-6 py-3">Signals</th>
                                <th class="px-6 py-3">Scanned At</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="flaggedTableBody" class="divide-y divide-neutral-100 dark:divide-white/5">
                            <tr><td colspan="6" class="px-6 py-8 text-center text-neutral-400">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Pending corrections -->
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-neutral-900 dark:text-white">Pending Corrections</h2>
                <span id="correctionsCount" class="text-xs font-semibold text-neutral-400"></span>
            </div>
            <div class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400 border-collapse">
                        <thead class="text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300">
                            <tr>
                                <th class="px-6 py-3">Student</th>
                                <th class="px-6 py-3">Class</th>
                                <th class="px-6 py-3">Change</th>
                                <th class="px-6 py-3">Reason</th>
                                <th class="px-6 py-3">Requested By</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="correctionsTableBody" class="divide-y divide-neutral-100 dark:divide-white/5">
                            <tr><td colspan="6" class="px-6 py-8 text-center text-neutral-400">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/attendance-review/index.js'])
@endpush
