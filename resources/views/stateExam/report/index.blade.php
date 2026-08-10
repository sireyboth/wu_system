@extends('layouts.dashboard')
@section('title', 'Attendance Report')
@section('content')

<div class="mb-8 flex items-center justify-between flex-wrap gap-4">
    <div>
        <a href="{{ route('stateExam.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-neutral-500 hover:text-indigo-600 dark:text-neutral-400 dark:hover:text-indigo-400 transition-colors mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            ត្រឡប់ក្រោយ (Back to Exam Rooms)
        </a>
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
            របាយការណ៍អវត្តមានប្រឡង <span class="text-indigo-600 dark:text-indigo-400">(Exam Attendance Report)</span>
        </h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            សង្ខេបស្ថិតិវត្តមាន/អវត្តមាននិស្សិត តាមម៉ោងប្រឡងនីមួយៗ
        </p>
    </div>
    <button id="refreshReportBtn" type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-neutral-700 dark:text-neutral-200 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-sm hover:bg-neutral-50 dark:hover:bg-white/5 transition-all active:scale-95">
        <svg id="refreshReportIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
        </svg>
        ផ្ទុកឡើងវិញ (Refresh)
    </button>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wide text-neutral-400">បន្ទប់សរុប</span>
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
            </div>
        </div>
        <div id="kpiTotalRooms" class="text-2xl font-black text-neutral-900 dark:text-white">—</div>
        <div class="text-xs text-neutral-400">Total Exam Rooms</div>
    </div>

    <div class="p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wide text-neutral-400">និស្សិតសរុប</span>
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
            </div>
        </div>
        <div id="kpiTotalStudents" class="text-2xl font-black text-neutral-900 dark:text-white">—</div>
        <div class="text-xs text-neutral-400">Total Registered Students</div>
    </div>

    <div class="p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wide text-neutral-400">អវត្តមានសរុប</span>
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
            </div>
        </div>
        <div id="kpiTotalAbsent" class="text-2xl font-black text-neutral-900 dark:text-white">—</div>
        <div class="text-xs text-neutral-400">Total Absences (all sessions)</div>
    </div>

    <div class="p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wide text-neutral-400">អត្រាមកវត្តមាន</span>
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <div id="kpiAttendanceRate" class="text-2xl font-black text-emerald-600 dark:text-emerald-400">—</div>
        <div class="text-xs text-neutral-400">Overall Attendance Rate</div>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 p-6 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-1">វត្តមាន vs អវត្តមាន តាមម៉ោង</h3>
        <p class="text-xs text-neutral-400 mb-4">Present vs Absent by session</p>
        <div class="relative h-72">
            <canvas id="sessionBarChart"></canvas>
        </div>
    </div>

    <div class="p-6 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-1">អត្រាមកវត្តមានសរុប</h3>
        <p class="text-xs text-neutral-400 mb-4">Overall attendance rate</p>
        <div class="relative h-72">
            <canvas id="overallDoughnutChart"></canvas>
        </div>
    </div>
</div>

<div class="p-6 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm mb-8">
    <h3 class="text-sm font-bold text-neutral-900 dark:text-white mb-1">បន្ទប់ប្រឡងតាមជំនាញ</h3>
    <p class="text-xs text-neutral-400 mb-4">Exam rooms by major</p>
    <div class="relative h-64">
        <canvas id="majorBarChart"></canvas>
    </div>
</div>

{{-- Session breakdown table --}}
<div class="overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
    <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5">
        <h3 class="text-sm font-bold text-neutral-900 dark:text-white">សេចក្តីលម្អិតតាមម៉ោង (Session Breakdown)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400">
            <thead class="text-xs uppercase text-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300">
                <tr>
                    <th class="px-6 py-3">Session</th>
                    <th class="px-6 py-3">Total Students</th>
                    <th class="px-6 py-3">Present</th>
                    <th class="px-6 py-3">Absent</th>
                    <th class="px-6 py-3">Attendance Rate</th>
                </tr>
            </thead>
            <tbody id="sessionTableBody" class="divide-y divide-neutral-200 dark:divide-white/5">
                <tr><td colspan="5" class="px-6 py-10 text-center text-neutral-400">កំពុងទាញយកទិន្នន័យ...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    window.EXAM_STATE_REPORT_URL = @json(route('exam-states.report'));
</script>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/stateExam/report.js'])
@endpush
