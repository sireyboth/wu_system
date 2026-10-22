<div id="rosterModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <div id="rosterModalCard"
         class="w-full max-w-5xl bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden max-h-[85vh] flex flex-col">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900 shrink-0">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">Roster — <span id="rosterClassCode" class="font-mono text-indigo-600"></span></h3>
            <div class="flex items-center gap-3">
                <button type="button" id="rosterHistoryBtn" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Attendance History
                </button>
                <button type="button" onclick="RosterModal.toggle(false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="px-6 pt-4 space-y-3 shrink-0">
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Add an individual student <span class="normal-case font-normal text-neutral-400">(any batch/major — for a retake, add-subject, or a student borrowed for just this class)</span></label>
                <div class="relative">
                    <input id="rosterAddStudentInput" type="text" placeholder="Type a student name or code..." autocomplete="off"
                        class="block w-full py-2.5 px-3 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-neutral-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-950 dark:border-white/10 dark:placeholder-neutral-500 dark:text-white transition-all">
                    <div id="rosterAddStudentResults" class="hidden absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-lg max-h-56 overflow-y-auto"></div>
                </div>

                <div id="rosterAddStudentPreview" class="hidden mt-3 p-4 bg-indigo-50/60 dark:bg-indigo-500/5 border border-indigo-200/70 dark:border-indigo-500/20 rounded-xl">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-2">Confirm before adding</p>
                    <div id="rosterAddStudentPreviewBody" class="text-sm text-neutral-700 dark:text-neutral-300 space-y-1"></div>
                    <div class="flex justify-end gap-2 mt-3">
                        <button type="button" id="rosterAddStudentCancel" class="px-3 py-1.5 text-xs font-semibold text-neutral-500 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-lg">Cancel</button>
                        <button type="button" id="rosterAddStudentConfirm" class="px-4 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Add to Class</button>
                    </div>
                </div>
            </div>

            <p class="text-xs text-neutral-500 dark:text-neutral-400">Type a score and click away (or press Tab) to save it — each cell saves on its own, and shows the last saved value on reload.</p>
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
                <input id="rosterSearchInput" type="text" placeholder="Search student name or code..." autocomplete="off"
                    class="block w-full py-2 ps-9 pe-3 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-neutral-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-950 dark:border-white/10 dark:placeholder-neutral-500 dark:text-white transition-all">
            </div>
        </div>
        <div class="p-6 pt-3 overflow-y-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-neutral-500 dark:text-neutral-400 uppercase">
                    <tr>
                        <th class="pb-2 pr-3">Student</th>
                        <th class="pb-2 pr-3">Status</th>
                        <th class="pb-2 pr-2 text-center">Homework</th>
                        <th class="pb-2 pr-2 text-center">Quiz</th>
                        <th class="pb-2 pr-2 text-center">Assignment</th>
                        <th class="pb-2 pr-2 text-center">Midterm</th>
                        <th class="pb-2 pr-2 text-center">Final</th>
                        <th class="pb-2 pr-3 text-center">Attendance <span class="normal-case font-normal text-neutral-400">(auto)</span></th>
                        <th class="pb-2 pr-3 text-center">Total Point</th>
                        <th class="pb-2 text-center">Grade Point</th>
                    </tr>
                </thead>
                <tbody id="rosterTableBody" class="divide-y divide-neutral-100 dark:divide-white/5">
                    <tr><td colspan="10" class="py-6 text-center text-neutral-400">Loading roster...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
