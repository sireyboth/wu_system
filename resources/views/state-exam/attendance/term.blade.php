@extends('layouts.state.public')
@section('title', $examTerm->title . ' — Exam Attendance')
@section('content')

<div class="mb-8">
    <a href="{{ route('state-exam.attendance.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-neutral-700 dark:text-neutral-200 bg-white/85 dark:bg-neutral-900/70 backdrop-blur-sm border border-neutral-200 dark:border-white/10 rounded-xl shadow-sm hover:shadow-md hover:border-indigo-400/60 dark:hover:border-indigo-500/40">
        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        ត្រឡប់ក្រោយ (Back to exams)
    </a>
</div>

<div class="text-center mb-12">
    <div class="inline-flex items-center gap-2 px-3 py-1 mb-4 text-[11px] font-bold tracking-widest uppercase rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-500/20">
        {{ $examTerm->category?->name_en }}
    </div>
    <h1 class="font-display text-3xl sm:text-4xl font-bold tracking-tight">{{ $examTerm->title }}</h1>
    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400 max-w-md mx-auto">
        ជ្រើសរើសម៉ោងប្រឡងដើម្បីបញ្ចូលអវត្តមាន
        <span class="block text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Select a time slot below to record attendance</span>
    </p>
</div>

@php $slots = $examTerm->time_slots ?? []; @endphp

@if (empty($slots))
    <div class="max-w-md mx-auto text-center">
        <p class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">គ្មានម៉ោងកំណត់ទេ</p>
        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">This exam term has no time slots set up yet — ask a registrar to add some.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach ($slots as $i => $label)
            <a href="{{ route('state-exam.attendance.search', [$examTerm, $i + 1]) }}"
               class="group relative overflow-hidden p-8 text-center bg-white/80 dark:bg-neutral-900/70 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 rounded-3xl shadow-sm hover:shadow-lg hover:border-indigo-400/60 dark:hover:border-indigo-500/40">

                <span class="pointer-events-none absolute -top-3 -right-2 font-display text-7xl font-bold text-neutral-900/[0.04] dark:text-white/[0.05] select-none">
                    {{ $i + 1 }}
                </span>

                <div class="relative">
                    <div class="mx-auto mb-5 flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white shadow-lg shadow-indigo-500/25">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div class="text-xl font-bold text-neutral-900 dark:text-white">{{ $label }}</div>
                    <div class="mt-1.5 inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                        Slot {{ $i + 1 }}
                    </div>

                    <div class="mt-5 pt-5 border-t border-neutral-100 dark:border-white/5 flex items-center justify-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                        ចូលបញ្ចូលទិន្នន័យ (Enter data)
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif

@endsection
