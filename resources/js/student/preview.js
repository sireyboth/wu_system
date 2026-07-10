import { CONFIG } from './config.js';
import { escapeHtml } from './form-utils.js';
import { togglePreviewModal } from './ui.js';

/**
 * Fetches one student's full record and renders it into the read-only preview modal.
 */
export async function handlePreviewAction(ApiService, id) {
    togglePreviewModal(true);

    const contentContainer = document.getElementById('previewModalContent');
    if (!contentContainer) return;

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
    const nationality = student.nationality || person.nationality || {};
    const address = Array.isArray(person.addresses) && person.addresses.length > 0 ? person.addresses[0] : null;
    const guardian = Array.isArray(student.guardians) && student.guardians.length > 0 ? student.guardians[0] : null;

    const nameKhmer = escapeHtml(
        person.first_name_kh || person.last_name_kh ? `${person.last_name_kh} ${person.first_name_kh}`.trim() : 'N/A'
    );
    const nameEnglish = escapeHtml(
        person.first_name || person.last_name ? `${person.last_name} ${person.first_name}`.trim() : 'N/A'
    );

    const dobFormatted = person.dob
        ? new Date(person.dob).toLocaleDateString(CONFIG.LOCALE || 'km-KH', { day: '2-digit', month: 'short', year: 'numeric' })
        : 'N/A';
    const admissionFormatted = student.admission_at
        ? new Date(student.admission_at).toLocaleDateString(CONFIG.LOCALE || 'km-KH', { day: '2-digit', month: 'short', year: 'numeric' })
        : 'N/A';

    /**
     * Helper to render highly noticeable visual badge components for exam metrics
     */
    const renderExamBadge = (value) => {
        const valString = String(value ?? '').trim();
        if (valString === '1') {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 mt-0.5">ជាប់ (Passed)</span>`;
        } else if (valString === '2') {
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 mt-0.5">ធ្លាក់ (Failed)</span>`;
        }
        return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-neutral-100 text-neutral-600 dark:bg-white/5 dark:text-neutral-400 mt-0.5">N/A</span>`;
    };

    contentContainer.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
                <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">១. អត្តសញ្ញាណផ្ទាល់ខ្លួន (Personal Identity)</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
                    <div><span class="text-neutral-400 block">គោត្តនាម-នាមខ្លួន (KH):</span> <span class="font-bold text-neutral-900 dark:text-white">${nameKhmer}</span></div>
                    <div><span class="text-neutral-400 block">Full Name (EN):</span> <span class="font-bold text-neutral-900 dark:text-white">${nameEnglish}</span></div>
                    <div><span class="text-neutral-400 block">ភេទ (Sex):</span> <span class="capitalize font-semibold">${escapeHtml(person.sex ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">សញ្ជាតិ (Nationality):</span> <span class="font-semibold text-neutral-900 dark:text-white">${escapeHtml(nationality.name_kh || nationality.name || 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">ថ្ងៃខែឆ្នាំកំណើត (DOB):</span> <span class="font-mono">${dobFormatted}</span></div>
                    <div><span class="text-neutral-400 block">លេខទូរស័ព្ទ (Phone):</span> <span class="font-mono">${escapeHtml(person.phone ?? 'N/A')}</span></div>
                    <div class="col-span-2"><span class="text-neutral-400 block">អ៊ីមែល (Email):</span> <span class="font-mono truncate block">${escapeHtml(person.email ?? 'N/A')}</span></div>
                </div>
            </div>

            <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
                <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">២. ព័ត៌មានសិក្សា (Academic Records)</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
                    <div><span class="text-neutral-400 block">អត្តសញ្ញាណប័ណ្ណនិស្សិត (ID Code):</span> <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">${escapeHtml(student.code ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">ស្ថានភាពនិស្សិត (Status):</span> <span class="uppercase font-bold text-emerald-500">${escapeHtml(student.status ?? 'Active')}</span></div>
                    <div><span class="text-neutral-400 block">ជំនាញឯកទេស (Major):</span> <span class="font-semibold">${escapeHtml(major.name ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">ជំនាន់សិក្សា (Batch):</span> <span class="font-mono">${escapeHtml(batch.name ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">កាលបរិច្ឆេទចូលរៀន (Admission Date):</span> <span class="font-mono">${admissionFormatted}</span></div>
                    <div><span class="text-neutral-400 block">លេខតុ បាក់ឌុប (BACC II Code):</span> <span class="font-mono">${escapeHtml(student.bacc_2_code ?? 'N/A')}</span></div>

                    <div class="pt-1"><span class="text-neutral-400 block">ប្រឡងចូល:</span> ${renderExamBadge(student.entrance_exam)}</div>
                    <div class="pt-1"><span class="text-neutral-400 block">ប្រឡងបញ្ចប់ការសិក្សា:</span> ${renderExamBadge(student.exit_exam)}</div>
                </div>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
            <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">៣. អាសយដ្ឋានបច្ចុប្បន្ន (Current Residential Address)</h4>
            ${address ? `
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs">
                    <div><span class="text-neutral-400 block">ផ្ទះ/ផ្លូវ (Street/House):</span> <span class="font-medium text-neutral-900 dark:text-white">${escapeHtml(address.street ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">ខេត្ត/រាជធានី (Province):</span> <span class="font-medium text-neutral-900 dark:text-white">${escapeHtml(address.province?.name_kh || address.province?.name || 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">ស្រុក/ខណ្ឌ (District):</span> <span class="font-medium text-neutral-900 dark:text-white">${escapeHtml(address.district?.name_kh ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">ឃុំ/សង្កាត់ (Commune):</span> <span class="font-medium text-neutral-900 dark:text-white">${escapeHtml(address.commune?.name_kh ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">ភូមិ (Village):</span> <span class="font-medium text-neutral-900 dark:text-white">${escapeHtml(address.village?.name_kh ?? 'N/A')}</span></div>
                </div>
            ` : `<p class="text-xs text-neutral-400 italic">គ្មានការកំណត់ព័ត៌មានអាសយដ្ឋានទេ</p>`}
        </div>

        <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
            <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">៤. ព័ត៌មានអាណាព្យាបាល (Primary Guardian Relations)</h4>
            ${guardian ? `
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div><span class="text-neutral-400 block">ឈ្មោះខ្មែរ (Name KH):</span> <span class="font-bold text-neutral-900 dark:text-white">${escapeHtml(guardian.person?.last_name_kh ?? '')} ${escapeHtml(guardian.person?.first_name_kh ?? '')}</span></div>
                    <div><span class="text-neutral-400 block">Name EN:</span> <span class="font-semibold text-neutral-900 dark:text-white">${escapeHtml(guardian.person?.last_name ?? '')} ${escapeHtml(guardian.person?.first_name ?? '')}</span></div>
                    <div><span class="text-neutral-400 block">ត្រូវជា (Relationship):</span> <span class="font-medium text-indigo-600 dark:text-indigo-400">${escapeHtml(guardian.pivot?.relationship ?? 'N/A')}</span></div>
                    <div><span class="text-neutral-400 block">លេខទូរស័ព្ទ (Phone):</span> <span class="font-mono font-medium">${escapeHtml(guardian.person?.phone ?? 'N/A')}</span></div>
                </div>
            ` : `<p class="text-xs text-neutral-400 italic">គ្មានទិន្នន័យអាណាព្យាបាលភ្ជាប់ជាមួយឡើយ</p>`}
        </div>`;
}
