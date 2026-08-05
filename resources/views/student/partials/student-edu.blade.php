<div id="panel-academic" class="tab-panel hidden space-y-6">
    <div>
        <h4
            class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">
            ព័ត៌មានរដ្ឋបាលសិក្សា (Institutional Academic Routing)</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អត្តលេខនិស្សិត
                    (Student Code)</label>
                <input required type="text" name="code" placeholder="e.g., ST-2026-048"
                    class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ជំនាន់សិក្សា
                    (Batch)</label>
                <select required name="batch_id" id="academic_batch_id"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    <option value="" disabled selected>-- ជ្រើសរើសជំនាន់ --</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ជំនាញឯកទេស
                    (Major)</label>
                <select required name="major_id" id="academic_major_id"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    <option value="" disabled selected>-- ជ្រើសរើសជំនាញ --</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">វេននិស្សិត
                    (Student Shift) <span class="text-rose-500">*</span></label>
                <select required name="shift_id" id="student-shift"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសវេនសិក្សា --</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">ក្រុមសិក្សា
                    (Group) <span class="text-rose-500">*</span></label>
                <select required name="group_id" id="student-group"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសក្រុមសិក្សា --</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">វគ្គសិក្សា
                    (Intake) <span class="text-rose-500">*</span></label>
                <select required name="intake" id="student_intake"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសវគ្គសិក្សា --</option>
                    <option value="primary">វគ្គទី១ (First Intake)</option>
                    <option value="secondary">វគ្គទី២ (Second Intake)</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">ប្រភេទអាហាររូបករណ័
                    (Scholarship) <span class="text-rose-500">*</span></label>
                <select required name="scholarship" id="student_scholarship"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសអាហាររូបករណ័ --</option>
                    <option value="none">គ្មាន</option>
                    <option value="ministry">ក្រសួង</option>
                    <option value="prince">សម្ដេច</option>
                    <option value="school">សាលា</option>
                </select>
            </div>
            <div class="relative group">
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អតីតវិទ្យាល័យ
                    (High School)<span class="text-rose-500">*</span></label>
                <input type="text" name="from_school" placeholder="e.g., វិទ្យាល័យ ជា ស៊ីម សាមគ្គី"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
        </div>
    </div>

    <div class="pt-2">
        <h4
            class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">
            កាលបរិច្ឆេទ និងលទ្ធផលវាយតម្លៃ (Metrics & Admission Timelines)</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ស្ថានភាពការសិក្សា
                    (Enrollment Status)</label>
                <select name="status_id" id="student-status"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    <option value="" disabled selected>-- ជ្រើសរើសស្ថានភាពសិក្សា--</option>
                </select>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃចូលរៀនដំបូង
                    (Official Admission Date)</label>
                <input required type="date" name="admission_date"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លទ្ធផលប្រឡងចូល
                    (Entrance Result)</label>
                <select name="entrance_exam"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសលទ្ធផល --</option>
                    <option value="none">មិនទាន់ប្រឡង</option>
                    <option value="passed">ជាប់ (Passed)</option>
                    <option value="failed">ធ្លាក់ (Failed)</option>
                </select>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លទ្ធផលប្រឡងបញ្ចប់ការសិក្សា
                    (Exit Result)</label>
                <select name="exit_exam"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសលទ្ធផល --</option>
                    <option value="none">មិនទាន់ប្រឡង</option>
                    <option value="passed">ជាប់ (Passed)</option>
                    <option value="failed">ធ្លាក់ (Failed)</option>
                </select>
            </div>
        </div>
    </div>
</div>
