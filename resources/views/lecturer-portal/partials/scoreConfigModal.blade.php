<div id="scoreConfigModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <div id="scoreConfigModalCard"
         class="w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-white">My Score Config — <span id="scoreConfigClassCode" class="font-mono text-indigo-600"></span></h3>
            <button type="button" onclick="ScoreConfigModal.toggle(false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="scoreConfigForm" class="p-6 space-y-4 bg-white dark:bg-neutral-900">
            <input type="hidden" name="class_id" id="scoreConfigClassId">
            <p class="text-xs text-neutral-500 dark:text-neutral-400">Your own point split for this class only. Every other class — even another section of the same subject — keeps its own config; changing this never affects anyone else's class.</p>

            <div class="grid grid-cols-2 gap-4">
                @foreach (['homework_max' => 'Homework', 'quiz_max' => 'Quiz', 'assignment_max' => 'Assignment', 'midterm_max' => 'Midterm', 'final_max' => 'Final', 'attendance_max' => 'Attendance'] as $field => $label)
                    <div>
                        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">{{ $label }}</label>
                        <input type="number" min="0" max="100" name="{{ $field }}" value="0"
                            data-field="{{ $field }}"
                            class="score-max-input w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </div>
                @endforeach
            </div>

            <div id="scoreConfigTotalBanner" class="flex items-center justify-between px-4 py-3 rounded-xl border text-sm font-bold">
                <span>Total</span>
                <span id="scoreConfigTotalValue">0 / 100</span>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-neutral-100 dark:border-white/5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Total Weeks</label>
                    <input type="number" min="1" max="52" name="total_weeks" value="15"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Sessions / Week</label>
                    <input type="number" min="1" max="14" name="sessions_per_week" value="2"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
                <p class="col-span-2 text-xs text-neutral-400">Used to work out each student's Attendance score: (Present + Excused) ÷ (Weeks × Sessions/Week) × Attendance points above.</p>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900">
                <button type="button" onclick="ScoreConfigModal.toggle(false)"
                    class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-indigo-500/20 active:scale-95 rounded-xl transition-all duration-200">
                    Save Config
                </button>
            </div>
        </form>
    </div>
</div>
