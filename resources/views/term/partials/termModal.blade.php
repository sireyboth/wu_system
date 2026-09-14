<div id="termModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <div id="modalCard"
         class="w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900">
            <h3 id="modalTitle" class="text-lg font-bold text-neutral-900 dark:text-white">Create New Term</h3>
            <button type="button" onclick="AppModal.toggle(false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="termForm" class="p-6 space-y-5 bg-white dark:bg-neutral-900">
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Academic Year</label>
                    <input required type="number" name="year" placeholder="e.g., 2026"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Semester</label>
                    <select required name="semester"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Code</label>
                    <input required type="text" name="code" placeholder="e.g., S1-2026"
                        class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Display Name</label>
                    <input required type="text" name="name" placeholder="e.g., 2026-2027"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Start Date</label>
                    <input required type="date" name="start_date"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">End Date</label>
                    <input required type="date" name="end_date"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
            </div>

            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="is_active" value="1"
                    class="w-4 h-4 rounded border-neutral-300 dark:border-white/20 text-indigo-600 focus:ring-4 focus:ring-indigo-500/10">
                <span class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">
                    Make this the active term (deactivates every other term)
                </span>
            </label>

            <div class="relative group">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Remark</label>
                <textarea name="remark" rows="2" placeholder="Optional notes..."
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 resize-none"></textarea>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900">
                <button type="button" onclick="AppModal.toggle(false)"
                    class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-indigo-500/20 active:scale-95 rounded-xl transition-all duration-200">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
