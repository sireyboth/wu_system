@extends('layouts.state.public')
@section('title', $roundLabel . ' — Exam Attendance')
@section('content')

<div class="mb-8 fade-up">
    <a href="{{ route('state-exam.attendance.index') }}"
       class="group inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-neutral-700 dark:text-neutral-200 bg-white/85 dark:bg-neutral-900/70 backdrop-blur-sm border border-neutral-200 dark:border-white/10 rounded-xl shadow-sm hover:shadow-md hover:border-indigo-400/60 dark:hover:border-indigo-500/40 hover:-translate-x-0.5 transition-all duration-300">
        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        ត្រឡប់ក្រោយ (Back to sessions)
    </a>

    <div class="mt-5 flex items-center gap-3">
        <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white shadow-lg shadow-indigo-500/25 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h1 class="font-display text-2xl font-bold tracking-tight">{{ $roundLabel }}</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">ស្វែងរកបន្ទប់ដើម្បីបញ្ចូល/កែប្រែអវត្តមាន</p>
        </div>
    </div>
</div>

<div class="relative mb-6 fade-up" style="animation-delay:120ms">
    <div class="absolute inset-y-0 left-0 flex items-center ps-4 pointer-events-none">
        <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
        </svg>
    </div>
    <input id="roomSearchInput" type="text" placeholder="ស្វែងរកបន្ទប់, ជំនាញ, សញ្ញាបត្រ..." autocomplete="off"
        class="block w-full p-4 ps-11 text-sm bg-white/80 dark:bg-neutral-900/70 backdrop-blur-sm border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-400 dark:focus:border-indigo-500/50 dark:placeholder-neutral-500 outline-none transition-all" />
</div>

<div id="roomList" class="space-y-3 fade-up" style="animation-delay:200ms"></div>

<script>
    window.EXAM_ROUND = {{ $round }};
    window.EXAM_ROUND_LABEL = @json($roundLabel);
</script>

@endsection

@push('modals')
{{-- Toast notification stack (top-right, custom — no admin/SweetAlert dependency on public).
     Lives in the 'modals' stack (rendered as a direct child of <body> by the
     layout) instead of inline here — a `fixed` element nested inside
     <main>'s positioned/z-indexed stacking context can't paint above later
     siblings like <footer> no matter how high its own z-index is set. --}}
<div id="stateToastStack" class="fixed top-5 right-5 z-[70] flex flex-col gap-2 w-[calc(100%-2.5rem)] max-w-sm"></div>

{{-- Custom absence-entry modal — same reasoning, must live outside <main>. --}}
<div id="absenceModal" class="fixed inset-0 z-[60] hidden items-start justify-center overflow-y-auto p-4 py-8 bg-neutral-900/50 dark:bg-black/70 backdrop-blur-sm transition-opacity duration-300">
    <div id="absenceModalCard"
         class="w-full max-w-sm my-auto max-h-[85dvh] flex flex-col bg-white dark:bg-neutral-900 rounded-3xl shadow-2xl border border-neutral-200/70 dark:border-white/10 overflow-hidden scale-95 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]">

        <div class="shrink-0 relative px-6 pt-7 pb-5 text-center bg-gradient-to-br from-indigo-600 to-indigo-800">
            <button id="absenceCloseBtn" type="button"
                    class="absolute top-3 right-3 flex items-center justify-center w-8 h-8 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="mx-auto mb-3 flex items-center justify-center w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 id="absenceModalTitle" class="text-lg font-bold text-white">—</h3>
            <p id="absenceModalSubtitle" class="text-xs font-medium text-indigo-200 mt-0.5">—</p>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto p-6 custom-scrollbar">
            <label class="block text-center text-xs font-bold uppercase tracking-widest text-neutral-400 mb-3">
                ចំនួនអវត្តមាន (Absent Count)
            </label>

            <div class="flex items-center justify-center gap-4">
                <button id="absenceMinusBtn" type="button"
                        class="flex items-center justify-center w-11 h-11 rounded-2xl bg-neutral-100 dark:bg-white/5 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-white/10 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                </button>
                <input id="absenceInput" type="number" min="0" inputmode="numeric"
                       class="w-24 text-center text-3xl font-black bg-transparent text-neutral-900 dark:text-white outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                <button id="absencePlusBtn" type="button"
                        class="flex items-center justify-center w-11 h-11 rounded-2xl bg-neutral-100 dark:bg-white/5 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-white/10 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" /></svg>
                </button>
            </div>
            <p id="absenceMaxHint" class="text-center text-xs text-neutral-400 dark:text-neutral-500 mt-3">—</p>
            <p id="absenceError" class="hidden text-center text-xs font-bold text-rose-500 mt-2"></p>
        </div>

        <div class="shrink-0 flex gap-2.5 p-6 pt-0">
            <button id="absenceCancelBtn" type="button"
                    class="flex-1 py-3.5 rounded-2xl font-bold text-sm text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-white/5 hover:bg-neutral-200 dark:hover:bg-white/10 transition-colors">
                បោះបង់
            </button>
            <button id="absenceSaveBtn" type="button"
                    class="flex-1 py-3.5 rounded-2xl font-bold text-sm text-white bg-indigo-600 shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-500/30 active:scale-95 transition-all">
                រក្សាទុក
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
    @vite(['resources/js/stateExam/attendance.js'])
@endpush
