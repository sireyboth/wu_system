@extends('layouts.state.public')
@section('title', $roundLabel . ' — Exam Attendance')
@section('content')

<div class="mb-6">
    <a href="{{ route('stateExam.attendance.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; ត្រឡប់ក្រោយ (Back)</a>
    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white mt-2">{{ $roundLabel }}</h1>
    <p class="text-sm text-neutral-500 dark:text-neutral-400">ស្វែងរកបន្ទប់ដើម្បីបញ្ចូល/កែប្រែអវត្តមាន</p>
</div>

<div class="relative mb-5">
    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
        </svg>
    </div>
    <input id="roomSearchInput" type="text" placeholder="ស្វែងរកបន្ទប់, ជំនាញ, សញ្ញាបត្រ..." autocomplete="off"
        class="block w-full p-3 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all" />
</div>

<div id="roomList" class="space-y-3"></div>

<script>
    window.EXAM_ROUND = {{ $round }};
    window.EXAM_ROUND_LABEL = @json($roundLabel);
</script>

@endsection

@push('scripts')
    @vite(['resources/js/stateExam/attendance.js'])
@endpush
