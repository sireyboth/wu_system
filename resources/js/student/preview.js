import { CONFIG } from './config.js';
import { escapeHtml } from './form-utils.js';
import { togglePreviewModal } from './ui.js';

const LOCALE = CONFIG.LOCALE || 'en-GB';

function formatDate(value) {
    if (!value) return null;
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return null;
    return d.toLocaleDateString(LOCALE, { day: '2-digit', month: 'short', year: 'numeric' });
}

function lookupLabel(item) {
    if (!item) return null;
    return item.name_kh || item.name || item.name_en || item.shortcut || null;
}

function initials(nameKh, nameEn) {
    const source = (nameEn || nameKh || '').trim();
    if (!source) return '?';
    const parts = source.split(/\s+/).filter(Boolean);
    return parts.slice(0, 2).map((p) => p[0]?.toUpperCase() ?? '').join('') || '?';
}

/** One label/value pair, consistent across every section. */
function field(label, value, opts = {}) {
    const display = value === null || value === undefined || value === '' ? '<span class="text-neutral-300 dark:text-neutral-600 italic">—</span>' : value;
    const mono = opts.mono ? ' font-mono' : '';
    return `
        <div class="${opts.span ?? ''}">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-1">${label}</div>
            <div class="text-sm font-semibold text-neutral-900 dark:text-white${mono}">${display}</div>
        </div>`;
}

function sectionCard(icon, title, subtitle, innerHtml) {
    return `
        <div class="bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-white/10 rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">${icon}</div>
                <div>
                    <h4 class="text-sm font-bold text-neutral-900 dark:text-white">${title}</h4>
                    ${subtitle ? `<p class="text-xs text-neutral-400">${subtitle}</p>` : ''}
                </div>
            </div>
            ${innerHtml}
        </div>`;
}

const ICONS = {
    person: '<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    academic: '<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 17.219M12 14l-6.16-3.422A12.083 12.083 0 003 17.219"/></svg>',
    history: '<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    address: '<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    guardian: '<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 10-8 0"/></svg>',
};

function examBadge(value) {
    const v = String(value ?? '').trim();
    if (v === '1') return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">ជាប់ Passed</span>`;
    if (v === '2') return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">ធ្លាក់ Failed</span>`;
    return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-neutral-100 text-neutral-500 dark:bg-white/5 dark:text-neutral-400">N/A</span>`;
}

const ADDRESS_LABELS = { current: 'អាសយដ្ឋានបច្ចុប្បន្ន (Current Address)', birth: 'អាសយដ្ឋានកំណើត (Birth Address)' };

function renderAddress(address) {
    const label = ADDRESS_LABELS[address.type] ?? escapeHtml(address.type ?? 'Address');
    const parts = [
        lookupLabel(address.village) && `ភូមិ ${lookupLabel(address.village)}`,
        lookupLabel(address.commune) && `ឃុំ/សង្កាត់ ${lookupLabel(address.commune)}`,
        lookupLabel(address.district) && `ស្រុក/ខណ្ឌ ${lookupLabel(address.district)}`,
        lookupLabel(address.province) && `ខេត្ត/រាជធានី ${lookupLabel(address.province)}`,
    ].filter(Boolean);

    return `
        <div class="border border-neutral-100 dark:border-white/10 rounded-xl p-4">
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-2">${label}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                ${field('ផ្ទះ/ផ្លូវ (Street / House No.)', escapeHtml(address.street || address.house_no || '') || null)}
                ${field('ទីតាំង (Location)', parts.length ? escapeHtml(parts.join(', ')) : null)}
            </div>
        </div>`;
}

function renderGuardian(guardian, index) {
    const phones = Array.isArray(guardian.phones) ? guardian.phones.filter(Boolean) : [];
    return `
        <div class="border border-neutral-100 dark:border-white/10 rounded-xl p-4">
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-3">អាណាព្យាបាល #${index + 1}${guardian.relationship ? ` — ${escapeHtml(guardian.relationship)}` : ''}</div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-3">
                ${field('ឈ្មោះខ្មែរ (Name KH)', escapeHtml(guardian.name_kh) || null)}
                ${field('Name EN', escapeHtml(guardian.name_en) || null)}
                ${field('មុខរបរ (Job)', escapeHtml(guardian.job) || null)}
                ${field('លេខទូរស័ព្ទ (Phone)', phones.length ? escapeHtml(phones.join(', ')) : null, { mono: true })}
            </div>
        </div>`;
}

function historyChip(entry) {
    const pieces = [lookupLabel(entry.batch), lookupLabel(entry.major), lookupLabel(entry.shift), lookupLabel(entry.campus), lookupLabel(entry.group)].filter(Boolean);
    return pieces.map((p) => `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-neutral-100 dark:bg-white/5 text-neutral-600 dark:text-neutral-300">${escapeHtml(p)}</span>`).join('');
}

function renderHistoryRow(entry) {
    const isCurrent = !!entry.is_current;
    const termLabel = entry.term ? escapeHtml(entry.term.code || entry.term.name || '—') : 'គ្មានឆមាស (No term)';
    const date = formatDate(entry.effective_date);

    return `
        <div class="relative pl-6 pb-5 last:pb-0 border-l-2 ${isCurrent ? 'border-indigo-500' : 'border-neutral-200 dark:border-white/10'} ml-1.5">
            <span class="absolute -left-[7px] top-0.5 w-3 h-3 rounded-full ${isCurrent ? 'bg-indigo-500 ring-4 ring-indigo-100 dark:ring-indigo-500/20' : 'bg-neutral-300 dark:bg-neutral-600'}"></span>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <span class="text-sm font-bold text-neutral-900 dark:text-white">Year ${entry.year_level ?? '—'}${entry.semester ? `, Semester ${entry.semester}` : ''}</span>
                ${isCurrent ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 text-white">CURRENT</span>' : ''}
                <span class="text-xs text-neutral-400 font-mono">${termLabel}</span>
                ${date ? `<span class="text-xs text-neutral-400">· ${date}</span>` : ''}
            </div>
            <div class="flex flex-wrap gap-1.5">
                ${historyChip(entry) || '<span class="text-xs text-neutral-300 italic">No details recorded</span>'}
                ${lookupLabel(entry.status) ? `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">${escapeHtml(lookupLabel(entry.status))}</span>` : ''}
            </div>
        </div>`;
}

export async function handlePreviewAction(ApiService, id) {
    togglePreviewModal(true);

    const contentContainer = document.getElementById('previewModalContent');
    if (!contentContainer) return;

    contentContainer.innerHTML = `
        <div class="flex flex-col items-center justify-center py-24 gap-3">
            <div class="w-8 h-8 rounded-full border-2 border-indigo-600 border-t-transparent animate-spin"></div>
            <span class="text-xs text-neutral-400">កំពុងទាញយកទិន្នន័យគ្រប់ជ្រុងជ្រោយ...</span>
        </div>`;

    const [studentRes, historyRes] = await Promise.all([
        ApiService.request(`${CONFIG.API_BASE}/${id}`),
        ApiService.request(`${CONFIG.API_BASE}/${id}/academic-history`),
    ]);

    if (studentRes.error) {
        contentContainer.innerHTML = `<p class="text-center text-rose-500 py-20">បរាជ័យក្នុងការទាញយកទិន្នន័យ។</p>`;
        return;
    }

    const student = studentRes.data.data || studentRes.data;
    const person = student.person || {};
    const history = (historyRes.error ? [] : (historyRes.data?.data ?? historyRes.data ?? []))
        .slice()
        .sort((a, b) => (a.is_current === b.is_current ? 0 : a.is_current ? -1 : 1) || (b.effective_date ?? '').localeCompare(a.effective_date ?? ''));

    const addresses = Array.isArray(person.addresses) ? person.addresses : [];
    const guardians = Array.isArray(student.guardians) ? student.guardians : [];
    const phones = Array.isArray(person.phones) ? person.phones.filter(Boolean) : [];

    const nameKhmer = (person.first_name_kh || person.last_name_kh) ? `${person.last_name_kh ?? ''} ${person.first_name_kh ?? ''}`.trim() : null;
    const nameEnglish = (person.first_name || person.last_name) ? `${person.last_name ?? ''} ${person.first_name ?? ''}`.trim() : null;

    const canAttend = student.status?.can_attend;
    const statusBadgeClass = canAttend
        ? 'bg-emerald-500/15 text-emerald-100 border-emerald-300/30'
        : 'bg-rose-500/15 text-rose-100 border-rose-300/30';

    contentContainer.innerHTML = `
        <div class="relative px-8 pt-8 pb-6 bg-gradient-to-br from-indigo-600 to-indigo-800 text-white shrink-0">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-2xl font-bold shrink-0">
                    ${escapeHtml(initials(nameKhmer, nameEnglish))}
                </div>
                <div class="min-w-0">
                    <h3 class="text-xl font-bold truncate">${escapeHtml(nameKhmer || nameEnglish || 'N/A')}</h3>
                    <p class="text-sm text-indigo-100 truncate">${escapeHtml(nameEnglish && nameKhmer ? nameEnglish : '')}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-white/15 border border-white/20">${escapeHtml(student.code ?? 'N/A')}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border ${statusBadgeClass}">${escapeHtml(lookupLabel(student.status) ?? 'Unknown')}</span>
                        ${student.year_level ? `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/10 border border-white/20">Year ${student.year_level}${student.semester ? `, Sem ${student.semester}` : ''}</span>` : ''}
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-5">
                ${[lookupLabel(student.major), lookupLabel(student.batch), lookupLabel(student.shift), lookupLabel(student.campus), lookupLabel(student.group)]
                    .filter(Boolean)
                    .map((v) => `<span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white/10 border border-white/10">${escapeHtml(v)}</span>`)
                    .join('')}
            </div>
        </div>

        <div class="p-8 space-y-6 bg-neutral-50 dark:bg-neutral-950">

            ${sectionCard(ICONS.person, 'អត្តសញ្ញាណផ្ទាល់ខ្លួន (Personal Identity)', null, `
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-4">
                    ${field('ភេទ (Sex)', person.sex ? escapeHtml(person.sex) : null)}
                    ${field('សញ្ជាតិ (Nationality)', lookupLabel(person.nationality) ? escapeHtml(lookupLabel(person.nationality)) : null)}
                    ${field('ថ្ងៃខែឆ្នាំកំណើត (DOB)', formatDate(person.dob), { mono: true })}
                    ${field('លេខទូរស័ព្ទ (Phone)', phones.length ? escapeHtml(phones.join(', ')) : null, { mono: true })}
                    ${field('អ៊ីមែល (Email)', person.email ? escapeHtml(person.email) : null, { mono: true, span: 'col-span-2 sm:col-span-1' })}
                </div>
            `)}

            ${sectionCard(ICONS.academic, 'ព័ត៌មានសិក្សា (Academic Profile)', null, `
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-4">
                    ${field('សញ្ញាបត្រ (Degree Type)', student.degree_label?.kh || student.degree_label?.en || student.degree_type ? escapeHtml(student.degree_label?.kh || student.degree_label?.en || student.degree_type) : null)}
                    ${field('ការទូទាត់ (Payment As)', student.payment_as ? escapeHtml(student.payment_as) : null)}
                    ${field('ចូលរៀនជា (Intake)', student.intake ? escapeHtml(student.intake) : null)}
                    ${field('អាហារូបករណ៍ (Scholarship)', student.scholarship ? escapeHtml(String(student.scholarship)) : null)}
                    ${field('សាលារៀនចាស់ (From School)', student.from_school ? escapeHtml(student.from_school) : null)}
                    ${field('និស្សិតរៀនសង (Is Restudy)', student.is_restudy ? '<span class="text-amber-600 dark:text-amber-400">Yes</span>' : 'No')}
                    ${field('លេខតុ បាក់ឌុប (BACC II Code)', student.bacc_2_code ? escapeHtml(student.bacc_2_code) : null, { mono: true })}
                    ${field('កាលបរិច្ឆេទចូលរៀន (Admission Date)', formatDate(student.admission_date), { mono: true })}
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-1">ប្រឡងចូល (Entrance Exam)</div>
                        ${examBadge(student.entrance_exam)}
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-1">ប្រឡងបញ្ចប់ (Exit Exam)</div>
                        ${examBadge(student.exit_exam)}
                    </div>
                </div>
            `)}

            ${sectionCard(ICONS.history, 'ប្រវត្តិសិក្សា — ឆមាសបច្ចុប្បន្ន និងមុន (Academic History — Current &amp; Previous)', 'Every semester this student has been recorded under, most recent first', `
                ${history.length
                    ? `<div>${history.map(renderHistoryRow).join('')}</div>`
                    : '<p class="text-sm text-neutral-400 italic">គ្មានប្រវត្តិសិក្សាទេ (No academic history recorded yet)</p>'}
            `)}

            ${sectionCard(ICONS.address, 'អាសយដ្ឋាន (Addresses)', null, `
                ${addresses.length
                    ? `<div class="space-y-3">${addresses.map(renderAddress).join('')}</div>`
                    : '<p class="text-sm text-neutral-400 italic">គ្មានការកំណត់ព័ត៌មានអាសយដ្ឋានទេ</p>'}
            `)}

            ${sectionCard(ICONS.guardian, 'អាណាព្យាបាល (Guardians)', null, `
                ${guardians.length
                    ? `<div class="space-y-3">${guardians.map(renderGuardian).join('')}</div>`
                    : '<p class="text-sm text-neutral-400 italic">គ្មានទិន្នន័យអាណាព្យាបាលភ្ជាប់ជាមួយឡើយ</p>'}
            `)}
        </div>`;
}
