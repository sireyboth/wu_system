{{-- Bulk version of advanceSemesterModal.blade.php — every field defaults
     to "— No change —" and stays untouched per-student unless explicitly
     set here, so a registrar can e.g. bump Year Level for a whole filtered
     major without forcing everyone into the same Batch. --}}
<div id="bulkAdvanceSemesterModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <div id="bulkAdvanceSemesterCard"
         class="w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900">
            <div>
                <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Bulk Advance Semester</h3>
                <p id="bulkAdvanceSemesterScope" class="text-xs text-neutral-400 mt-0.5">—</p>
            </div>
            <button type="button" data-close-modal="bulk-advance-semester" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-6 pt-4">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">
                Record this advance under
            </label>
            <div class="flex items-center gap-2">
                <select id="bulkAdvanceSemesterTermId" name="term_id"
                    class="w-full px-4 py-2.5 text-sm bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 rounded-xl text-indigo-700 dark:text-indigo-300 font-bold outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </select>
                <a href="{{ route('term.index') }}" class="text-xs underline hover:no-underline text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Manage terms</a>
            </div>
            <p class="text-[11px] text-neutral-400 mt-1">Defaults to whichever active term covers today — pick a different one if this batch runs on its own calendar.</p>
        </div>

        <form id="bulkAdvanceSemesterForm" class="p-6 space-y-5 bg-white dark:bg-neutral-900">
            <p class="text-xs text-neutral-500 dark:text-neutral-400 -mt-1">
                Leave a field as <b>— No change —</b> to keep each student's own current value. Only fields you set here get applied.
            </p>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Batch</label>
                    <select name="batch_id" id="bulk_advance_batch_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                        <option value="">— No change —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Major</label>
                    <select name="major_id" id="bulk_advance_major_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                        <option value="">— No change —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Shift</label>
                    <select name="shift_id" id="bulk_advance_shift_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                        <option value="">— No change —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Group</label>
                    <select name="group_id" id="bulk_advance_group_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                        <option value="">— No change —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Campus</label>
                    <select name="campus_id" id="bulk_advance_campus_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                        <option value="">— No change —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status_id" id="bulk_advance_status_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                        <option value="">— No change —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Year Level</label>
                    <input type="number" name="year_level" id="bulk_advance_year_level" min="1" max="10" placeholder="No change"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Semester</label>
                    <select name="semester" id="bulk_advance_semester"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500">
                        <option value="">— No change —</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900">
                <button type="button" data-close-modal="bulk-advance-semester"
                    class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 shadow-md hover:shadow-teal-500/20 active:scale-95 rounded-xl transition-all duration-200">
                    Advance Selected
                </button>
            </div>
        </form>
    </div>
</div>
