// const ApiService = window.ApiService; // <-- Add this line here
const { error, data } = await window.ApiService.request(`${CONFIG.API_BASE}/${id}`);

 // Change "const DOM = {" to "window.DOM = {"
// 1. Link to shared global system modules
window.DOM = {

    form: document.getElementById('studentForm'),
    tableBody: document.getElementById('student-table-body'),
    searchInput: document.getElementById('studentSearchInput'),
    loader: document.getElementById('loading-overlay'),
    modal: document.getElementById('studentModal'),
    modalCard: document.getElementById('modalCard'),
    modalTitle: document.getElementById('modalTitle'),
    submitBtn: document.getElementById('studentForm')?.querySelector('button[type="submit"]')
};

// 1. Link to shared global system modules
const DOM = window.DOM;


// 2. The rest of your preview logic remains exactly the same
DOM.tableBody?.addEventListener('click', function(e) {
    const btn = e.target.closest('button');
    if (!btn) return;

    const action = btn.getAttribute('data-action');
    const id = btn.getAttribute('data-id');

    if (action === 'preview') {
        handlePreviewAction(id);
    }
});

// 2. Add the Preview click listener directly inside preview.js
DOM.tableBody?.addEventListener('click', function(e) {
    const btn = e.target.closest('button');
    if (!btn) return;

    const action = btn.getAttribute('data-action');
    const id = btn.getAttribute('data-id');

    // Only intercept the preview action here
    if (action === 'preview') {
        handlePreviewAction(id);
    }
});

/**
 * Visibility state helper for Preview Layout Shells
 */
function togglePreviewModal(show) {
    const el = document.getElementById('previewModal');
    if (!el) return;
    if (show) {
        el.classList.remove('hidden');
        setTimeout(() => el.firstElementChild.classList.remove('scale-95'), 10);
    } else {
        el.firstElementChild.classList.add('scale-95');
        setTimeout(() => el.classList.add('hidden'), 150);
    }
}
// Make toggle global so HTML button onclick handles can run it
window.togglePreviewModal = togglePreviewModal;


async function handlePreviewAction(id) {
    togglePreviewModal(true);
    const contentContainer = document.getElementById('previewModalContent');
    if (!contentContainer) return;

    // Set initial loading layout state inside target viewport
    contentContainer.innerHTML = `
        <div class="flex flex-col items-center justify-center py-20 gap-3">
            <div class="w-8 h-8 rounded-full border-2 border-indigo-600 border-t-transparent animate-spin"></div>
            <span class="text-xs text-neutral-400">កំពុងទាញយកទិន្នន័យគ្រប់ជ្រុងជ្រោយ...</span>
        </div>`;

    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
    if (error) {
        contentContainer.innerHTML = `<p class="text-center text-rose-500 py-10">បរាជ័យក្នុងការទាញយកទិន្នន័យ។</p>`;
        return;
    }

    const student = data.data || data;
    const person = student.person || {};
    const major = student.major || {};
    const batch = student.batch || {};
    const address = Array.isArray(person.addresses) && person.addresses.length > 0 ? person.addresses[0] : null;
    const guardian = Array.isArray(student.guardians) && student.guardians.length > 0 ? student.guardians[0] : null;

    // Build the data markup
    contentContainer.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
                <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">១. អត្តសញ្ញាណផ្ទាល់ខ្លួន (Personal Identity)</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
                    <div><span class="text-neutral-400 block">គោត្តនាម-នាមខ្លួន (KH):</span> <span class="font-bold text-neutral-900 dark:text-white">${person.last_name_kh ?? ''} ${person.first_name_kh ?? ''}</span></div>
                    <div><span class="text-neutral-400 block">Full Name (EN):</span> <span class="font-bold text-neutral-900 dark:text-white">${person.last_name ?? ''} ${person.first_name ?? ''}</span></div>
                    <div><span class="text-neutral-400 block">ភេទ (Sex):</span> <span class="capitalize font-semibold">${person.sex ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ថ្ងៃខែឆ្នាំកំណើត (DOB):</span> <span class="font-mono">${person.dob ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">លេខទូរស័ព្ទ (Phone):</span> <span class="font-mono">${person.phone ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">អ៊ីមែល (Email):</span> <span class="font-mono truncate block">${person.email ?? 'N/A'}</span></div>
                </div>
            </div>

            <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
                <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">២. ព័ត៌មានសិក្សា (Academic Records)</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
                    <div><span class="text-neutral-400 block">អត្តសញ្ញាណប័ណ្ណនិស្សិត (ID Code):</span> <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">${student.code ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ស្ថានភាពនិស្សិត (Status):</span> <span class="uppercase font-bold text-emerald-500">${student.status ?? 'Active'}</span></div>
                    <div><span class="text-neutral-400 block">ជំនាញឯកទេស (Major):</span> <span class="font-semibold">${major.name ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ជំនាន់សិក្សា (Batch):</span> <span class="font-mono">${batch.name ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">កាលបរិច្ឆេទចូលរៀន (Admission Date):</span> <span class="font-mono">${student.admission_at ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">លេខតុ បាក់ឌុប (BACC II Code):</span> <span class="font-mono">${student.bacc_2_code ?? 'N/A'}</span></div>
                </div>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
            <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">៣. អាសយដ្ឋានបច្ចុប្បន្ន (Current Residential Address)</h4>
            ${address ? `
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs">
                    <div><span class="text-neutral-400 block">ផ្ទះ/ផ្លូវ (Street/House):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.street ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ខេត្ត/រាជធានី (Province):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.province?.name_kh || address.province?.name || 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ស្រុក/ខណ្ឌ (District):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.district ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ឃុំ/សង្កាត់ (Commune):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.commune ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ភូមិ (Village):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.village ?? 'N/A'}</span></div>
                </div>
            ` : `<p class="text-xs text-neutral-400 italic">គ្មានការកំណត់ព័ត៌មានអាសយដ្ឋានទេ (No address mapping attached to this record).</p>`}
        </div>

        <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
            <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">៤. ព័ត៌មានអាណាព្យាបាល (Primary Guardian Relations)</h4>
            ${guardian ? `
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div><span class="text-neutral-400 block">ឈ្មោះខ្មែរ (Name KH):</span> <span class="font-bold text-neutral-900 dark:text-white">${guardian.person?.last_name_kh ?? ''} ${guardian.person?.first_name_kh ?? ''}</span></div>
                    <div><span class="text-neutral-400 block">Name EN:</span> <span class="font-semibold text-neutral-900 dark:text-white">${guardian.person?.last_name ?? ''} ${guardian.person?.first_name ?? ''}</span></div>
                    <div><span class="text-neutral-400 block">ត្រូវជា (Relationship):</span> <span class="font-medium text-indigo-600 dark:text-indigo-400">${guardian.pivot?.relationship ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">លេខទូរស័ព្ទ (Phone):</span> <span class="font-mono font-medium">${guardian.person?.phone ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ភេទ (Sex):</span> <span class="capitalize">${guardian.person?.sex ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">មុខរបរ (Occupation):</span> <span>${guardian.occupation ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">អាណាព្យាបាលចម្បង?:</span> <span class="font-bold text-neutral-900 dark:text-white">${guardian.pivot?.is_primary ? 'បាទ/ចាស (Yes)' : 'ទេ (No)'}</span></div>
                </div>
            ` : `<p class="text-xs text-neutral-400 italic">គ្មានទិន្នន័យអាណាព្យាបាលភ្ជាប់ជាមួយឡើយ (No guardian logs associated with this student profile).</p>`}
        </div>
    `;
}
