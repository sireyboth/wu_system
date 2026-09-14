@extends('layouts.dashboard')
@section('title', 'My Classes')
@section('content')

    <x-core.page-header title="ថ្នាក់រៀនរបស់ខ្ញុំ (My Classes)" subtitle="Only classes you're assigned to teach. Set your own scoring split and record student scores." />

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
                        <th scope="col" class="px-6 py-4 text-center">Enrolled</th>
                        <th scope="col" class="px-6 py-4">My Score Config</th>
                        <th scope="col" class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="my-class-table-body" class="divide-y divide-neutral-200 dark:divide-white/5">
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center">
                            <span class="text-neutral-500">Loading data...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @include('lecturer-portal.partials.scoreConfigModal')
    @include('lecturer-portal.partials.rosterModal')
    @include('lecturer-portal.partials.attendanceModal')
@endsection

@push('scripts')
    @vite(['resources/js/lecturer-portal/index.js'])
@endpush
