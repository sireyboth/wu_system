<div id="student-status-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-white/10 overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-white/10">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-white">បញ្ចូលទិន្នន័យសញ្ញាបណ្ណបណ្ដោះអាសន្ន</h2>
            <button type="button" onclick="closeStudentStatusModal()"
                class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 dark:hover:text-white dark:hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="student-status-form" class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
    @csrf

    <div class="p-4 bg-neutral-50 dark:bg-white/5 border border-neutral-200 dark:border-white/10 rounded-xl space-y-3">
        <div class="flex justify-between items-start">
            <div>
                <h3 id="infoNameKh" class="font-bold text-neutral-900 dark:text-white text-base">—</h3>
                <p id="infoNameEn" class="text-xs font-mono text-neutral-400 mt-0.5">—</p>
            </div>
            <div id="infoSexBadge"></div>
        </div>
        
        <div class="grid grid-cols-2 gap-y-2 gap-x-4 pt-2.5 border-t border-neutral-200 dark:border-white/10 text-xs">
            <div>
                <span class="text-neutral-400 block">អត្តលេខ (Student ID)</span>
                <span id="infoCode" class="font-mono font-bold text-neutral-700 dark:text-neutral-300">—</span>
            </div>
            <div>
                <span class="text-neutral-400 block">ថ្ងៃខែឆ្នាំកំណើត (DOB)</span>
                <span id="infoDob" class="font-mono text-neutral-700 dark:text-neutral-300">—</span>
            </div>
            <div class="col-span-2">
                <span class="text-neutral-400 block">ជំនាញ (Major)</span>
                <span id="infoMajor" class="text-neutral-700 dark:text-neutral-300">—</span>
            </div>
        </div>
    </div>

    <input type="hidden" name="student_id" id="student_id" required>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">កាលបរិច្ឆេទចេញសញ្ញាបត្រ(Date Issue)</label>
        <input type="date" name="dateEng" id="dateEng" required
            class="w-full rounded-lg border border-neutral-300 dark:border-white/10 dark:bg-white/5 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all
                [&::-webkit-calendar-picker-indicator]:dark:invert">
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">កាលបរិច្ឆេទចន្ទគតិ (Khmer Date)</label>
        <input type="text" name="dateKh" id="dateKh" readonly
            placeholder="បំពេញដោយស្វ័យប្រវត្តិ"
            class="w-full rounded-lg border border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5 dark:text-white px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">កាលបរិច្ឆេទសាកល (Khmer Date - Short)</label>
        <input type="text" name="shortDateKh" id="shortDateKh" readonly
            placeholder="បំពេញដោយស្វ័យប្រវត្តិ"
            class="w-full rounded-lg border border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5 dark:text-white px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">លេខសញ្ញាបណ្ណ (Certificate No.)</label>
        <input type="text" name="cer_no" id="cer_no" required placeholder="លេខចេញសញ្ញាបត្រ"
            class="w-full rounded-lg border border-neutral-300 dark:border-white/10 dark:bg-white/5 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">ស្ថានភាព (Status)</label>
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                    bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400
                    border border-amber-200 dark:border-amber-500/20">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            មិនទាន់បោះពុម្ព (Not Print Yet)
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">សម្គាល់ (Remark)</label>
        <textarea name="remark" id="remark" rows="3" placeholder="ដាក់ស្អីក៏ដាក់ទៅ គ្មានអាណាដឹងទេកន្លែងនិង"
            class="w-full rounded-lg border border-neutral-300 dark:border-white/10 dark:bg-white/5 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"></textarea>
    </div>
</form>

        <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5">
            <button type="button" onclick="closeStudentStatusModal()"
                class="px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/10 transition-all">
                បោះបង់ (Cancel)
            </button>
            <button type="submit" form="student-status-form"
                class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">
                រក្សាទុក (Save)
            </button>
        </div>
    </div>
</div>

<script>
    function openStudentStatusModal() {
        const modal = document.getElementById('student-status-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeStudentStatusModal() {
        const modal = document.getElementById('student-status-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('student-status-form').reset();
        document.getElementById('dateKh').value = '';
        document.getElementById('shortDateKh').value = '';
    }

    document.getElementById('dateEng').addEventListener('change', function (e) {
        const val = e.target.value;
        if (!val) {
            document.getElementById('dateKh').value = '';
            document.getElementById('shortDateKh').value = '';
            return;
        }

        const [year, month, day] = val.split('-').map(Number);
        const khmer = momentkh.fromGregorian(year, month, day);

        document.getElementById('dateKh').value = momentkh.format(khmer);
        document.getElementById('shortDateKh').value = momentkh.format(khmer, 'Ds ខែM ឆ្នាំc');
    });

    document.getElementById('student-status-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('StudentStatusCertificate.store') }}", {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeStudentStatusModal();
        })
        .catch(err => {
            console.error(err);
        });
    });
</script>