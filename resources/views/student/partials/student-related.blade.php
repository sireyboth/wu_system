<div id="panel-guardian" class="tab-panel hidden space-y-6">
    <div>
        <h4
            class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">
            ព័ត៌មានផ្ទាល់ខ្លួនអាណាព្យាបាល (Guardian Core Profile & Identity)</h4>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 pt-2">
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

    <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-1 gap-5 pt-2">
        <div>
            <label
                class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អាសយដ្ឋានអាណាព្យាបាល​ (មិនតម្រូវ)</label>
            <input required type="tel" name="guardians[0][addresses][0]" placeholder="e.g., Phnom Penh"
                class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
        </div>
    </div>
</div>
