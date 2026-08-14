@extends('layouts.state.open')
@section('title', 'កាតកាតព្វកិច្ចអ្នកឃ្លាំមើល (Invigilator Duty Card)')
@section('content')


  <!-- University logo — drop the real file at public/images/logo.png and this fills in automatically -->
    <div class="mx-auto mb-5 flex items-center justify-center w-40 h-40 rounded-2xl bg-white/80 dark:bg-white/5 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 shadow-md p-2.5">
        <img src="{{ asset('images/logo.png') }}" alt="Western University logo"
             class="w-full h-full object-contain"
             onerror="this.style.display='none'">
    </div>

<div class="text-center mb-12 fade-up">
    <div class="inline-flex items-center gap-2 px-3 py-1 mb-5 text-[11px] font-bold tracking-widest uppercase rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-500/20">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        Official Duty Roster
    </div>
    <h1 class="font-display text-3xl sm:text-4xl font-bold tracking-tight">កាតបញ្ចាក់អនុរក្សក្នុងការប្រឡង</h1>
    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400 max-w-lg mx-auto">
        ស្វែងរកឈ្មោះ ឬបន្ទប់ ដើម្បីមើលកាលវិភាគអនុរក្សនីមួយៗ
        <span class="block text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Search your name or a room number to find your invigilation duty — room, floor, and shift.</span>
    </p>
</div>

<div class="relative mb-8 max-w-xl mx-auto fade-up no-print" style="animation-delay:120ms">
    <div class="absolute inset-y-0 left-0 flex items-center ps-4 pointer-events-none">
        <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
        </svg>
    </div>
    <input id="invigilatorSearchInput" type="text" placeholder="ស្វែងរកឈ្មោះ, បន្ទប់, ជាន់..." autocomplete="off"
        class="block w-full p-4 ps-11 text-sm bg-white/80 dark:bg-neutral-900/70 backdrop-blur-sm border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-400 dark:focus:border-indigo-500/50 dark:placeholder-neutral-500 outline-none transition-all" />
</div>

<div id="dutyCardList" class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-5xl mx-auto items-start fade-up" style="animation-delay:200ms"></div>

@endsection

@push('scripts')
    @vite(['resources/js/stateExam/invigilators.js'])
@endpush
