<x-ui.modal id="stateExamModal" title="បន្ថែមបន្ទប់ប្រឡងថ្មី (Add Exam Room)" form-id="stateExamForm" max-width="max-w-3xl">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">បន្ទប់ (Room)</label>
            <input required type="text" name="room" autocomplete="off" placeholder="e.g., 104"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">វេន (Shift)</label>
            <input type="text" name="shift" autocomplete="off" placeholder="e.g., Morning"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ជំនាញ (Major)</label>
            <input required type="text" name="major" autocomplete="off" placeholder="e.g., Law"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">សញ្ញាបត្រ (Degree)</label>
            <input required type="text" name="degree" autocomplete="off" placeholder="e.g., BA"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
    </div>

    <!-- Majors Breakdown (dynamic, clonable) -->
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">ការបំបែកតាមជំនាញ និងអនុរក្ស (Majors Breakdown & Invigilators)</label>
            <button type="button" id="addMajorRowBtn"
                    class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                + បន្ថែមជំនាញ (Add Major)
            </button>
        </div>
        <div id="majorsContainer" class="space-y-2"></div>
        <p class="mt-1.5 text-[11px] text-neutral-400">ចំនួននិស្សិតសរុបនឹងគណនាដោយស្វ័យប្រវត្តិពីជំនាញនីមួយៗ (Student total is auto-computed from the rows above)</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ចំនួននិស្សិតសរុប (Student Total)</label>
            <input readonly type="number" name="student_total" value="0" tabindex="-1"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-500 dark:text-neutral-400 cursor-not-allowed outline-none">
        </div>
        <div class="relative group">
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃប្រឡង (Exam Date)</label>
            <input type="date" name="exam_date"
                   class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
        </div>
    </div>

    <!-- Attendance per Session (super admin manual override — mirrors the public attendance page) -->
    <div>
        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អវត្តមានតាមវគ្គ (Attendance per Session)</label>
        <div id="absencesContainer" class="grid grid-cols-1 sm:grid-cols-3 gap-3"></div>
        <p class="mt-1.5 text-[11px] text-neutral-400">ទុកចន្លោះទទេប្រសិនបើវគ្គនោះមិនទាន់បញ្ចូលទិន្នន័យ (Leave a session blank if it hasn't been recorded yet)</p>
    </div>

    <!-- Input Row: Remark -->
    <div class="relative group">
        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">សម្គាល់ (Remarks)</label>
        <textarea name="remark" rows="2" placeholder="Enter optional notes here..."
                  data-hint="Optional contextual notes or assignments details"
                  class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-4 focus:ring-indigo-500/40 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 focus:animate-[pulse_1.8s_infinite] transition-all duration-200 outline-none resize-none"></textarea>
        <div class="smart-hint pointer-events-none absolute right-3 -top-2 opacity-0 scale-95 translate-y-1 bg-indigo-600 text-white text-[11px] px-2.5 py-1 rounded-md shadow-md transition-all duration-200 font-medium"></div>
    </div>

    <x-slot:footer>
        <button type="button" onclick="AppModal.toggle(false)"
                class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
            បោះបង់
        </button>
        <button type="submit" form="stateExamForm"
                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-indigo-500/20 active:scale-95 rounded-xl transition-all duration-200">
            រក្សាទុក
        </button>
    </x-slot:footer>
</x-ui.modal>
