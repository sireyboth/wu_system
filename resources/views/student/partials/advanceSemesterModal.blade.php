{{-- Dedicated "advance to a new semester" action — a focused form with just
     the academic fields, instead of digging through the full edit-student
     form (identity/address/guardian tabs) just to bump a year level. Posts
     to students/{id}/advance-semester, which goes through the exact same
     history-preserving logic as a normal edit. --}}
<div id="advanceSemesterModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <div id="advanceSemesterCard"
         class="w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900">
            <div>
                <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Advance to a New Semester</h3>
                <p id="advanceSemesterStudentName" class="text-xs text-neutral-400 mt-0.5">—</p>
            </div>
            <button type="button" data-close-modal="advance-semester" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-6 pt-4">
            <div id="advanceSemesterTermBanner" class="flex items-center gap-2 text-xs px-3 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                Will be recorded under: <span id="advanceSemesterActiveTerm" class="font-bold">—</span>
                <a href="{{ route('term.index') }}" class="ml-auto underline hover:no-underline">Manage terms</a>
            </div>
        </div>

        <form id="advanceSemesterForm" class="p-6 space-y-5 bg-white dark:bg-neutral-900">
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Batch</label>
                    <select required name="batch_id" id="advance_batch_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Major</label>
                    <select required name="major_id" id="advance_major_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Shift</label>
                    <select required name="shift_id" id="advance_shift_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Group</label>
                    <select required name="group_id" id="advance_group_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Campus</label>
                    <select name="campus_id" id="advance_campus_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="">—</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Status</label>
                    <select required name="status_id" id="advance_status_id"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Year Level</label>
                    <input required type="number" name="year_level" id="advance_year_level" min="1" max="10"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Semester</label>
                    <select name="semester" id="advance_semester"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="">Not set</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
            </div>

            <p class="text-xs text-neutral-400 dark:text-neutral-500">
                Only changed fields create a new history record — the student's current semester stays exactly as it was.
            </p>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900">
                <button type="button" data-close-modal="advance-semester"
                    class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 shadow-md hover:shadow-teal-500/20 active:scale-95 rounded-xl transition-all duration-200">
                    Advance Semester
                </button>
            </div>
        </form>
    </div>
</div>
