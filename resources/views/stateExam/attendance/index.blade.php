@extends('layouts.state.public')
@section('title', 'ការចូលរួមប្រឡង (Exam Attendance)')
@section('content')

<div class="text-center mb-10">
    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">ជ្រើសរើសសម័យប្រឡង</h1>
    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">ជ្រើសរើសម៉ោងប្រឡងដើម្បីបញ្ចូលអវត្តមាន (Select a session to enter attendance)</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    @foreach ($rounds as $i => $label)
        <a href="{{ route('stateExam.attendance.search', $i + 1) }}"
           class="group p-8 text-center bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm hover:shadow-xl hover:border-indigo-500/50 hover:-translate-y-1 transition-all duration-300">
            <div class="mx-auto mb-4 flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-lg font-bold text-neutral-900 dark:text-white">{{ $label }}</div>
            <div class="text-xs text-neutral-400 mt-1">Session {{ $i + 1 }}</div>
        </a>
    @endforeach
</div>

@endsection
