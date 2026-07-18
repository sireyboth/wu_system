<div id="student-status-modal"
    class="fixed inset-0 z-50 items-center justify-center hidden px-4 bg-black/40 backdrop-blur-sm">
    <div
        class="w-full max-w-lg overflow-hidden bg-white border shadow-2xl dark:bg-neutral-900 rounded-2xl border-neutral-200 dark:border-white/10">

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

            <div
                class="p-4 space-y-3 border bg-neutral-50 dark:bg-white/5 border-neutral-200 dark:border-white/10 rounded-xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 id="infoNameKh" class="text-base font-bold text-neutral-900 dark:text-white">—</h3>
                        <p id="infoNameEn" class="text-xs font-mono text-neutral-400 mt-0.5">—</p>
                    </div>
                    <div id="infoSexBadge"></div>
                </div>

                <div
                    class="grid grid-cols-2 gap-y-2 gap-x-4 pt-2.5 border-t border-neutral-200 dark:border-white/10 text-xs">
                    <div>
                        <span class="block text-neutral-400">អត្តលេខ (Student ID)</span>
                        <span id="infoCode" class="font-mono font-bold text-neutral-700 dark:text-neutral-300">—</span>
                    </div>
                    <div>
                        <span class="block text-neutral-400">ថ្ងៃខែឆ្នាំកំណើត (DOB)</span>
                        <span id="infoDob" class="font-mono text-neutral-700 dark:text-neutral-300">—</span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-neutral-400">ជំនាញ (Major)</span>
                        <span id="infoMajor" class="text-neutral-700 dark:text-neutral-300">—</span>
                    </div>
                </div>
            </div>

            <input type="hidden" name="student_id" id="student_id" required>
            <input type="hidden" name="type" id="type" value="status">

            <div>
                <label class="block mb-1 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    កាលបរិច្ឆេទចេញសញ្ញាបត្រ (Date Issue)
                </label>
                <input type="date" name="issue_date" id="issue_date" required
                    class="w-full rounded-lg border border-neutral-300 dark:border-white/10 dark:bg-white/5 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:dark:invert">
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-neutral-700 dark:text-neutral-300">ថ្ងៃទីខ្មែរ (Khmer
                    Date - Full)</label>
                <input type="text" name="full_date_kh" id="full_date_kh" readonly placeholder="បំពេញដោយស្វ័យប្រវត្តិ"
                    class="w-full px-3 py-2 text-sm border rounded-lg border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5 dark:text-white">
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-neutral-700 dark:text-neutral-300">ថ្ងៃទីខ្មែរ (Khmer
                    Date - Short)</label>
                <input type="text" name="short_date_kh" id="short_date_kh" readonly
                    placeholder="បំពេញដោយស្វ័យប្រវត្តិ"
                    class="w-full px-3 py-2 text-sm border rounded-lg border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5 dark:text-white">
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-neutral-700 dark:text-neutral-300">លេខសញ្ញាបណ្ណ
                    (Certificate No.)</label>
                <input type="text" name="certificate_no" id="certificate_no" readonly
                    placeholder="បំពេញដោយស្វ័យប្រវត្តិ"
                    class="w-full px-3 py-2 text-sm border rounded-lg border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5 dark:text-white">
                <p class="mt-1 text-xs text-neutral-400" id="cerNoHint" style="display:none;">
                    លេខមើលជាមុន — លេខពិតប្រាកដនឹងបង្កើតនៅពេលរក្សាទុក (Preview only — final number generated on save)
                </p>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-neutral-700 dark:text-neutral-300">ស្ថានភាព
                    (Status)</label>
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    មិនទាន់បោះពុម្ព (Not Print Yet)
                </div>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-neutral-700 dark:text-neutral-300">សម្គាល់
                    (Remark)</label>
                <textarea name="remark" id="remark" rows="3" placeholder="ដាក់ស្អីក៏ដាក់ទៅ គ្មានអាណាដឹងទេកន្លែងហ្នឹងទេ"
                    class="w-full px-3 py-2 text-sm transition-all border rounded-lg border-neutral-300 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </form>

        <div
            class="flex items-center justify-end gap-2 px-6 py-4 border-t border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5">
            <button type="button" onclick="closeStudentStatusModal()"
                class="px-4 py-2 text-sm font-medium transition-all rounded-lg text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/10">
                បោះបង់ (Cancel)
            </button>
            <button type="submit" form="student-status-form" id="saveBtn"
                class="px-4 py-2 text-sm font-medium text-white transition-all bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                រក្សាទុក (Save)
            </button>
        </div>
    </div>
</div>

<script>
    function openStudentStatusModal(student) {
        // student = { id, name_kh, name_en, code, dob, major }
        document.getElementById('student_id').value = student.id;
        document.getElementById('infoNameKh').textContent = student.name_kh || '—';
        document.getElementById('infoNameEn').textContent = student.name_en || '—';
        document.getElementById('infoCode').textContent = student.code || '—';
        document.getElementById('infoDob').textContent = student.dob || '—';
        document.getElementById('infoMajor').textContent = student.major || '—';

        const modal = document.getElementById('student-status-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeStudentStatusModal() {
        const modal = document.getElementById('student-status-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('student-status-form').reset();
        document.getElementById('full_date_kh').value = '';
        document.getElementById('short_date_kh').value = '';
        document.getElementById('certificate_no').value = '';
        document.getElementById('cerNoHint').style.display = 'none';
    }

    document.getElementById('issue_date').addEventListener('change', function(e) {
        const val = e.target.value;

        if (!val) {
            document.getElementById('full_date_kh').value = '';
            document.getElementById('short_date_kh').value = '';
            document.getElementById('certificate_no').value = '';
            document.getElementById('cerNoHint').style.display = 'none';
            return;
        }

        const [year, month, day] = val.split('-').map(Number);
        const khmer = momentkh.fromGregorian(year, month, day);

        document.getElementById('full_date_kh').value = momentkh.format(khmer);
        document.getElementById('short_date_kh').value = momentkh.format(khmer, 'Ds ខែM ឆ្នាំc');

        fetchPreviewNumber(val);
    });

    function fetchPreviewNumber(issueDate) {
        const type = document.getElementById('type').value;

        fetch(`/api/v1/certificates/preview-number?type=${type}&issue_date=${issueDate}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('certificate_no').value = data.certificate_no;
                document.getElementById('cerNoHint').style.display = 'block';
            })
            .catch(err => console.error('Preview fetch failed', err));
    }

    document.getElementById('student-status-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.textContent = 'កំពុងរក្សាទុក...';

        const formData = new FormData(this);

        fetch("{{ route('certificates.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw err;
                    });
                }
                return res.json();
            })
            .then(data => {
                closeStudentStatusModal();
                window.dispatchEvent(new CustomEvent('certificate-saved', {
                    detail: data.data
                }));
                // e.g. reload the certificates table here
            })
            .catch(err => {
                console.error(err);
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'រក្សាទុក (Save)';
            });
    });
</script>
