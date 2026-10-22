<div id="attendanceHistoryModal"
     class="fixed inset-0 z-[60] invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <div id="attendanceHistoryModalCard"
         class="w-full max-w-6xl bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden max-h-[85vh] flex flex-col">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900 shrink-0">
            <div>
                <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Attendance History — <span id="attendanceHistoryClassCode" class="font-mono text-indigo-600"></span></h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Every session date this class has held. A date missing from the columns below is a week attendance was never taken.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" id="attendanceHistoryExportBtn" class="text-xs font-semibold text-neutral-600 dark:text-neutral-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5 5-5M12 15V3"/></svg>
                    Export
                </button>
                <button type="button" id="attendanceHistoryImportBtn" class="text-xs font-semibold text-neutral-600 dark:text-neutral-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M17 8l-5-5-5 5M12 3v12"/></svg>
                    Import
                </button>
                <input type="file" id="attendanceHistoryImportFile" accept=".xlsx,.xls,.csv" class="hidden">
                <button type="button" onclick="AttendanceHistoryModal.toggle(false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="px-6 py-3 border-b border-neutral-100 dark:border-white/5 shrink-0 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-500"></span> Present</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-rose-500"></span> Absent</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-500"></span> Late</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-sky-500"></span> Excused</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-neutral-200 dark:bg-white/10"></span> No record</span>
            <span id="attendanceHistorySessionCount" class="ml-auto font-semibold text-neutral-500 dark:text-neutral-400"></span>
        </div>

        <div class="overflow-auto grow">
            <table class="text-sm text-left border-collapse w-full">
                <thead id="attendanceHistoryHead" class="text-xs text-neutral-500 dark:text-neutral-400 uppercase sticky top-0 bg-white dark:bg-neutral-900 z-10"></thead>
                <tbody id="attendanceHistoryBody" class="divide-y divide-neutral-100 dark:divide-white/5">
                    <tr><td class="py-6 px-6 text-center text-neutral-400">Loading history...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
