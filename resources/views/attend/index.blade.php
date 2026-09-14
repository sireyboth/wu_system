@extends('layouts.state.public')
@section('title', 'ចូលរួមថ្នាក់រៀន (Class Attendance)')
@section('content')

<div class="text-center mb-10 fade-up">
    <div class="mx-auto mb-5 flex items-center justify-center w-20 h-20 rounded-2xl bg-white dark:bg-white/5 border border-neutral-200/80 dark:border-white/10 shadow-lg p-2">
        <img src="{{ asset('images/logo.png') }}" alt="Western University logo" class="w-full h-full object-contain">
    </div>

    <div class="inline-flex items-center gap-2 px-3 py-1 mb-5 text-[11px] font-bold tracking-widest uppercase rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-500/20">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        Attendance Scan
    </div>
    <h1 class="font-display text-3xl sm:text-4xl font-bold tracking-tight">ចូលរួមថ្នាក់រៀន</h1>
    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400 max-w-md mx-auto">
        បញ្ចូលកូដនិស្សិតរបស់អ្នកដើម្បីកត់ត្រាវត្តមាន
        <span class="block text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Enter your student code to mark yourself present in this class</span>
    </p>
</div>

<div id="attendCard" class="max-w-md mx-auto fade-up" style="animation-delay:120ms">
    <div class="bg-white/80 dark:bg-neutral-900/70 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 rounded-3xl shadow-sm p-6 sm:p-7">
        <label for="attendStudentCode" class="block text-xs font-bold uppercase tracking-widest text-neutral-400 mb-2">
            កូដនិស្សិត (Student Code)
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center ps-4 pointer-events-none">
                <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <input id="attendStudentCode" type="text" placeholder="e.g. 0025835" autocomplete="off" inputmode="numeric"
                class="block w-full p-4 ps-11 text-base sm:text-sm bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-400 dark:focus:border-indigo-500/50 dark:placeholder-neutral-500 outline-none transition-all" />
        </div>
        <input type="hidden" id="attendToken" value="{{ request('token') }}">
        <button id="attendSubmitBtn" type="button"
            class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 rounded-2xl shadow-lg shadow-indigo-500/25 active:scale-[0.98] transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            Mark Me Present
        </button>

        <div id="attendResult" class="hidden mt-5 px-4 py-4 rounded-2xl border text-sm text-center font-semibold"></div>

        @unless(request('token'))
            <p class="mt-4 text-xs text-center text-rose-500">No QR token found in this link — scan the lecturer's QR code again rather than opening this page directly.</p>
        @endunless
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/attend-public/index.js'])
@endpush
