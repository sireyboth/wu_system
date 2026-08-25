<x-ui.modal id="alertModal" title="បង្កើតការជូនដំណឹងថ្មី (New Alert)" form-id="alertForm" max-width="max-w-2xl">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ចំណងជើង (Title)</label>
            <input required type="text" name="title" autocomplete="off" placeholder="e.g., Submit Quarterly Report"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ចំណងជើងរង (Sub-title)</label>
            <input type="text" name="sub_title" autocomplete="off" placeholder="Optional"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
    </div>

    <div class="relative group">
        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">មាតិកា (Content)</label>
        <textarea required name="content" rows="2" placeholder="What is this alert about?"
                  class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none resize-none"></textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃចាប់ផ្តើម (Date Start)</label>
            <input required type="datetime-local" name="start_date"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃបញ្ចប់ (Date End)</label>
            <input required type="datetime-local" name="end_date"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ប្រភេទ (Category)</label>
            <input type="text" name="category" list="alertCategoryOptions" autocomplete="off" placeholder="e.g., Exam, Meeting, Deadline"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
            <datalist id="alertCategoryOptions">
                <option value="Exam"></option>
                <option value="Meeting"></option>
                <option value="Deadline"></option>
                <option value="Announcement"></option>
                <option value="Holiday"></option>
            </datalist>
        </div>
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ស្ថានភាព (Status)</label>
            <select name="status"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
                <option value="pending">មិនទាន់រួច (Pending)</option>
                <option value="completed">រួចរាល់ (Completed)</option>
            </select>
        </div>
    </div>

    <p class="flex items-center gap-1.5 text-[11px] text-neutral-400 dark:text-neutral-500 -mt-2">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
        គ្រប់ការជូនដំណឹងផ្ញើទៅ Telegram ដោយស្វ័យប្រវត្តិ រួមទាំងការជូនដំណឹងជាមុន ១ថ្ងៃ — every alert notifies via Telegram automatically, including a 1-day-before heads-up for both Start and End Date. No toggle needed.
    </p>

    <div class="relative group">
        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">សម្គាល់ (Note)</label>
        <textarea name="note" rows="2" placeholder="Optional notes..."
                  class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none resize-none"></textarea>
    </div>

    <!-- Repeating nag -->
    <div class="p-4 bg-neutral-50 dark:bg-white/[0.03] border border-neutral-200 dark:border-white/10 rounded-xl space-y-3">
        <div class="flex items-center justify-between">
            <div class="text-xs font-bold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">ការរំលឹកឡើងវិញ (Repeated reminders)</div>
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" id="remindEnabled" name="remind_enabled" value="1" class="sr-only peer">
                <div class="relative w-10 h-5.5 bg-neutral-300 dark:bg-white/10 rounded-full peer peer-checked:bg-indigo-600 transition-colors after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
            </label>
        </div>
        <div id="remindIntervalWrap" class="hidden">
            <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-1">រំលឹករៀងរាល់ ចាប់ពីថ្ងៃចាប់ផ្តើម រហូតដល់ធ្វើរួច (សូម្បីហួសកំណត់) — Remind every N minutes/hours/days from Start Date until Complete (even overdue)</label>
            <div class="flex gap-2">
                <input type="number" min="1" id="remindIntervalValue" placeholder="e.g., 5"
                       class="flex-1 px-3 py-2.5 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
                <select id="remindIntervalUnit"
                        class="px-3 py-2.5 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <option value="1">Minutes</option>
                    <option value="60">Hours</option>
                    <option value="1440">Days</option>
                </select>
            </div>
            <!-- Computed minutes actually submitted — kept in sync by JS from the value+unit pair above. -->
            <input type="hidden" name="remind_interval_minutes" id="remindIntervalMinutes">
        </div>
    </div>

    <!-- Repeat -->
    <div class="p-4 bg-neutral-50 dark:bg-white/[0.03] border border-neutral-200 dark:border-white/10 rounded-xl space-y-3">
        <div class="text-xs font-bold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">ធ្វើម្តងទៀត (Repeat)</div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-1">ប្រភេទ (Type)</label>
                <select name="repeat_type" id="repeatType"
                        class="w-full px-3 py-2.5 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <option value="none">Does not repeat</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div id="repeatIntervalWrap" class="hidden">
                <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-1">រៀងរាល់ (Every)</label>
                <input type="number" min="1" name="repeat_interval" value="1"
                       class="w-full px-3 py-2.5 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
            </div>
            <div id="repeatUntilWrap" class="hidden">
                <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-1">រហូតដល់ (Repeat until)</label>
                <input type="date" name="repeat_until"
                       class="w-full px-3 py-2.5 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
            </div>
        </div>
        <p class="text-[11px] text-neutral-400">នៅពេលធ្វើរួច ការជូនដំណឹងនេះនឹងផ្លាស់ទៅកាលបរិច្ឆេទបន្ទាប់ដោយស្វ័យប្រវត្តិ (On complete, this alert automatically re-arms to its next occurrence instead of staying done)</p>
    </div>

    <x-slot:footer>
        <button type="button" id="deleteAlertBtn"
                class="hidden mr-auto px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all duration-200">
            លុប (Delete)
        </button>
        <button type="button" onclick="AlertModal.close()"
                class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
            បោះបង់
        </button>
        <button type="submit" form="alertForm"
                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-indigo-500/20 active:scale-95 rounded-xl transition-all duration-200">
            រក្សាទុក
        </button>
    </x-slot:footer>
</x-ui.modal>
