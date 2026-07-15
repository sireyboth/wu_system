import { CONFIG } from './config.js';
import { escapeHtml } from './form-utils.js';

/**
 * Renders the student list into dom.tableBody.
 * Each row ships both a mobile-card layout and a desktop-row layout,
 * toggled with Tailwind's hidden/md: classes. That's fine at typical
 * page sizes (25-50 rows) once server-side pagination is in place —
 * it stops being fine if this is ever asked to render hundreds of
 * rows at once (see code review notes on pagination).
 */
export function renderTable(dom, students) {
    if (!dom.tableBody) return;

    if (!students || students.length === 0) {
        dom.tableBody.innerHTML =
            '<tr><td colspan="9" class="text-center py-10 text-neutral-500">រកមិនឃើញទិន្នន័យនិស្សិតទេ (No student records found).</td></tr>';
        return;
    }

    dom.tableBody.className =
        'grid grid-cols-1 gap-3 p-4 md:p-0 md:table-row-group md:gap-0 md:divide-y md:divide-neutral-200 md:dark:divide-white/5';

    dom.tableBody.innerHTML = students.map((student, index) => renderRow(student, index)).join('');
}

function renderRow(student, index) {
    const person = student.person || {};
    const major = student.major || {};
    const batch = student.batch || {};

    const nameKhmer = person.first_name_kh || person.last_name_kh
        ? escapeHtml(`${person.last_name_kh} ${person.first_name_kh}`.trim())
        : '<span class="text-neutral-400 italic">គ្មានទិន្នន័យ</span>';

    const nameEnglish = person.first_name || person.last_name
        ? escapeHtml(`${person.last_name} ${person.first_name}`.trim())
        : '<span class="text-neutral-400 italic">N/A</span>';

    const dobFormatted = person.dob
        ? new Date(person.dob).toLocaleDateString(CONFIG.LOCALE, { day: '2-digit', month: 'short', year: 'numeric' })
        : '<span class="text-neutral-400 italic">Unknown</span>';

    const officialDateFormatted = student.admission_at
        ? new Date(student.admission_at).toLocaleDateString(CONFIG.LOCALE, { day: '2-digit', month: 'short', year: 'numeric' })
        : '<span class="text-neutral-400 italic">Not set</span>';

    let sexLabel = '<span class="text-neutral-400">-</span>';
    if (person.sex === 'male') sexLabel = '<span class="font-medium text-neutral-800 dark:text-neutral-200">ប្រុស (M)</span>';
    if (person.sex === 'female') sexLabel = '<span class="font-medium text-pink-600 dark:text-pink-400">ស្រី (F)</span>';
    if (person.sex === 'other') sexLabel = '<span class="font-medium text-neutral-500">ផ្សេងៗ</span>';

    const statusValue = (student.status.name_en|| 'Active').toLowerCase();
    let statusBadgeClasses = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400';
    let statusTextKhmer = 'សិក្សា';
    if (statusValue === 'suspended' || statusValue === 'dropped') {
        statusBadgeClasses = 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400';
        statusTextKhmer = 'បោះបង់';
    } else if (statusValue === 'graduated') {
        statusBadgeClasses = 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400';
        statusTextKhmer = 'បញ្ចប់ការសិក្សា';
    }

    const initialsSource = (person.first_name_kh || person.first_name || student.code || '?').toString();
    const initials = escapeHtml(initialsSource.trim().charAt(0).toUpperCase());
    const code = student.code
        ? escapeHtml(student.code)
        : '<span class="text-rose-500 italic font-normal">Missing</span>';
    const majorName = escapeHtml(major.name ?? 'No Major Assigned');
    const batchName = escapeHtml(batch.name ?? 'N/A');

    return `
        <tr class="block md:table-row bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm md:shadow-none md:border-0 md:border-b md:rounded-none overflow-hidden md:overflow-visible">

            <td class="hidden md:table-cell px-6 py-4 text-neutral-400 font-mono text-xs">${index + 1}</td>

            <!-- MOBILE CARD -->
            <td class="block md:hidden p-0">
                <div class="flex items-center gap-3 p-4 border-b border-neutral-100 dark:border-white/5">
                    <div class="shrink-0 w-11 h-11 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                        ${initials}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-neutral-900 dark:text-neutral-100 text-[15px] leading-tight truncate">${nameKhmer}</div>
                        <div class="text-xs text-indigo-600 dark:text-indigo-400 font-medium truncate">${nameEnglish}</div>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-2 py-1 text-[10px] font-bold rounded-full whitespace-nowrap ${statusBadgeClasses}">
                        ${statusTextKhmer}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-2.5 px-4 py-3 text-xs">
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Student ID</span>
                        <span class="font-mono font-bold text-neutral-800 dark:text-neutral-200">${code}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Sex</span>
                        ${sexLabel}
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">DOB</span>
                        <span class="font-mono text-neutral-600 dark:text-neutral-400">${dobFormatted}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Admission</span>
                        <span class="font-mono text-neutral-600 dark:text-neutral-400">${officialDateFormatted}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Academic Plan</span>
                        <span class="font-semibold text-neutral-800 dark:text-neutral-200">${majorName}</span>
                        <span class="text-neutral-400"> · Batch ${batchName}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 border-t border-neutral-100 dark:border-white/5">
                    <button data-action="print" data-id="${student.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-indigo-600 active:bg-indigo-50 dark:active:bg-indigo-500/10">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.318 2.865a.75.75 0 01-.6.865H6.622a.75.75 0 01-.6-.865L6.34 18m11.32 0h1.093c.836 0 1.534-.64 1.6-1.474l.393-4.917a2.25 2.25 0 00-2.245-2.436H4.992a2.25 2.25 0 00-2.245 2.436l.393 4.917c.066.834.764 1.474 1.6 1.474H6.34m11.32 0H6.34m5.66-13.5v3.66m0 0a2.25 2.25 0 100-4.5v4.5z" />
                        </svg>
                        Print
                    </button>
                    <button data-action="edit" data-id="${student.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-amber-600 border-l border-r border-neutral-100 dark:border-white/5 active:bg-amber-50 dark:active:bg-amber-500/10">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                    <button data-action="delete" data-id="${student.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-rose-600 active:bg-rose-50 dark:active:bg-rose-500/10">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </td>

            <!-- DESKTOP ROW -->
            <td class="hidden md:table-cell px-6 py-4">
                <div class="flex flex-col gap-0.5">
                    <span class="font-bold text-neutral-900 dark:text-neutral-100 text-sm font-sans">${nameKhmer}</span>
                    <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">${nameEnglish}</span>
                </div>
            </td>
            <td class="hidden md:table-cell px-6 py-4 font-mono text-sm text-neutral-800 dark:text-neutral-200 font-bold">${code}</td>
            <td class="hidden md:table-cell px-6 py-4 text-sm">${sexLabel}</td>
            <td class="hidden md:table-cell px-6 py-4 text-xs font-mono text-neutral-600 dark:text-neutral-400">${dobFormatted}</td>
            <td class="hidden md:table-cell px-6 py-4">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">${majorName}</span>
                    <span class="text-xs text-neutral-400 font-mono">Batch: ${batchName}</span>
                </div>
            </td>
            <td class="hidden md:table-cell px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full ${statusBadgeClasses}">
                    ${statusTextKhmer} (${statusValue})
                </span>
            </td>
            <td class="hidden md:table-cell px-6 py-4 text-xs font-mono text-neutral-500">${officialDateFormatted}</td>
            <td class="hidden md:table-cell p-6 text-right">
                <div class="flex justify-end gap-1.5">
                    <button data-action="print" data-id="${student.id}" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-xl transition-colors" title="Print Certificate">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.318 2.865a.75.75 0 01-.6.865H6.622a.75.75 0 01-.6-.865L6.34 18m11.32 0h1.093c.836 0 1.534-.64 1.6-1.474l.393-4.917a2.25 2.25 0 00-2.245-2.436H4.992a2.25 2.25 0 00-2.245 2.436l.393 4.917c.066.834.764 1.474 1.6 1.474H6.34m11.32 0H6.34m5.66-13.5v3.66m0 0a2.25 2.25 0 100-4.5v4.5z" />
                        </svg>
                    </button>
                    <button data-action="edit" data-id="${student.id}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-xl transition-colors" title="Edit student">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button data-action="delete" data-id="${student.id}" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors" title="Delete student">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>`;
}
