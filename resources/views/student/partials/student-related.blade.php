<div id="panel-guardian" class="tab-panel hidden space-y-5">
    <div class="rounded-2xl border border-neutral-100 dark:border-white/5 bg-neutral-50/60 dark:bg-white/[0.02] p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <h4 class="text-sm font-bold text-neutral-800 dark:text-white">
                ព័ត៌មានផ្ទាល់ខ្លួនអាណាព្យាបាល (Guardian Core Profile & Identity)</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះអាណាព្យាបាលជាភាសាខ្មែរ</label>
                <input required type="text" name="guardians[0][name_kh]" placeholder="ឧ. សុខ ហេង"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះអាណាព្យាបាលជាភាសាអង់គ្លេស</label>
                <input required type="text" name="guardians[0][name_en]" placeholder="e.g., SOK HENG"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-neutral-100 dark:border-white/5 bg-neutral-50/60 dark:bg-white/[0.02] p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-600/10 text-emerald-600 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                </svg>
            </span>
            <h4 class="text-sm font-bold text-neutral-800 dark:text-white">
                ទំនាក់ទំនង និងមុខរបរ (Relationship & Contact)</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ត្រូវជា
                    (Relationship Connection)</label>
                <select name="guardians[0][relationship]"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    <option value="father">ឪពុក (Father)</option>
                    <option value="mother">ម្តាយ (Mother)</option>
                    <option value="other">អាណាព្យាបាល (Other)</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">មុខរបរ
                    (Guardian Occupation)</label>
                <input type="text" name="guardians[0][job]" placeholder="មុខរបរ ឬការងារបច្ចុប្បន្ន"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លេខទូរស័ព្ទទំនាក់ទំនងអាណាព្យាបាល (មិនតម្រូវ)</label>
                <input required type="tel" name="guardians[0][phones][0]" placeholder="e.g., 012778899"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 pt-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អាសយដ្ឋានអាណាព្យាបាល​ (មិនតម្រូវ)</label>
                <input type="tel" name="guardians[0][addresses][0]" placeholder="e.g., Phnom Penh"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
        </div>
    </div>
</div>
