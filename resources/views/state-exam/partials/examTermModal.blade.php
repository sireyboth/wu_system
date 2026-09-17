<x-ui.modal id="examTermModal" card-id="examTermModalCard" title-id="examTermModalTitle"
    title="បង្កើតការប្រឡងថ្មី (New Exam Term)" form-id="examTermForm"
    close-fn="ExamTermModal" max-width="max-w-lg">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300">ប្រភេទ (Category)</label>
                <button type="button" id="addCategoryBtn" title="Add a new category"
                    class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">+ New</button>
            </div>
            <select id="examTermCategorySelect" required
                class="w-full text-sm p-2.5 rounded-xl border border-neutral-200 dark:border-white/10 bg-white dark:bg-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">បរិវេណ (Campus)</label>
            <select id="examTermCampusSelect" required
                class="w-full text-sm p-2.5 rounded-xl border border-neutral-200 dark:border-white/10 bg-white dark:bg-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">ចំណងជើង (Title)</label>
        <input type="text" id="examTermTitleInput" required placeholder="e.g. Scholarship Exam — Sep 2026"
            class="w-full text-sm p-2.5 rounded-xl border border-neutral-200 dark:border-white/10 bg-white dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">ថ្ងៃប្រឡង (Exam Date)</label>
        <input type="date" id="examTermDateInput"
            class="w-full text-sm p-2.5 rounded-xl border border-neutral-200 dark:border-white/10 bg-white dark:bg-neutral-900 dark:text-white [color-scheme:light] dark:[color-scheme:dark] focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300">ម៉ោង (Time Slots)</label>
            <button type="button" id="addSlotBtn"
                class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">+ Add Slot</button>
        </div>
        <div id="examTermSlotsContainer" class="space-y-2"></div>
        <p class="mt-1.5 text-[11px] text-neutral-400">e.g. "7:30-9:00", "9:10-10:40" — these become the dropdown options when adding a room to this term.</p>
    </div>

    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
        <input type="checkbox" id="examTermActiveInput" checked
            class="w-4 h-4 rounded border-neutral-300 dark:border-white/20 text-indigo-600 focus:ring-4 focus:ring-indigo-500/10">
        <span class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">
            សកម្ម (Active) — on-site staff can search/find this term's rooms on the public attendance &amp; invigilator pages
        </span>
    </label>

    <x-slot:footer>
        <button type="button" onclick="ExamTermModal.toggle(false)"
            class="px-4 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-colors">
            បោះបង់ (Cancel)
        </button>
        <button type="submit" form="examTermForm" id="examTermSubmitBtn"
            class="px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
            បង្កើត (Create)
        </button>
    </x-slot:footer>
</x-ui.modal>
