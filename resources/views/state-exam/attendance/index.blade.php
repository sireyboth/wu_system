@extends('layouts.state.public')
@section('title', 'ការចូលរួមប្រឡង (Exam Attendance)')
@section('content')

<div class="text-center mb-14">
    <!-- University logo — drop the real file at public/images/logo.png and this fills in automatically -->
    <div class="mx-auto mb-5 flex items-center justify-center w-40 h-40 rounded-2xl bg-white/80 dark:bg-white/5 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 shadow-md p-2.5">
        <img src="{{ asset('images/logo.png') }}" alt="Western University logo"
             class="w-full h-full object-contain"
             onerror="this.style.display='none'">
    </div>

    <div class="inline-flex items-center gap-2 px-3 py-1 mb-5 text-[11px] font-bold tracking-widest uppercase rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-500/20">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
        Official Attendance System
    </div>
    <h1 class="font-display text-3xl sm:text-4xl font-bold tracking-tight">ជ្រើសរើសការប្រឡង</h1>
    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400 max-w-md mx-auto">
        ជ្រើសរើសការប្រឡងសកម្មដើម្បីបន្ត
        <span class="block text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Select which exam you're working today</span>
    </p>
</div>

@if ($examTerms->isEmpty())
    <div class="max-w-md mx-auto text-center">
        <p class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">គ្មានការប្រឡងសកម្មទេ</p>
        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">No exam term is active right now — ask a registrar to activate one.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($examTerms as $i => $term)
            <a href="{{ route('state-exam.attendance.term', $term) }}"
               class="group relative overflow-hidden p-8 text-center bg-white/80 dark:bg-neutral-900/70 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 rounded-3xl shadow-sm hover:shadow-lg hover:border-indigo-400/60 dark:hover:border-indigo-500/40">

                <div class="relative">
                    <div class="mx-auto mb-5 flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white shadow-lg shadow-indigo-500/25">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div class="text-xl font-bold text-neutral-900 dark:text-white">{{ $term->title }}</div>
                    <div class="mt-1.5 inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                        {{ $term->category?->name_en }}
                    </div>
                    @if ($term->exam_date)
                        <div class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">{{ $term->exam_date->format('d M Y') }}</div>
                    @endif

                    <div class="mt-5 pt-5 border-t border-neutral-100 dark:border-white/5 flex items-center justify-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                        ជ្រើសរើសម៉ោង (Choose a time slot)
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
