import { CONFIG } from './config.js';
import { escapeHtml } from './form-utils.js';

/**
 * Renders the exam-state (exam room) list into dom.tableBody.
 */
export function renderTable(dom, rooms, trashed = false) {
    if (!dom.tableBody) return;

    if (!rooms || rooms.length === 0) {
        const message = trashed
            ? 'ធុងសំរាមទទេ (Trash is empty).'
            : 'រកមិនឃើញទិន្នន័យបន្ទប់ប្រឡងទេ (No exam room records found).';
        dom.tableBody.innerHTML = `<tr><td colspan="10" class="text-center py-10 text-neutral-500">${message}</td></tr>`;
        return;
    }

    dom.tableBody.className =
        'grid grid-cols-1 gap-3 p-4 md:p-0 md:table-row-group md:gap-0 md:divide-y md:divide-neutral-200 md:dark:divide-white/5';

    dom.tableBody.innerHTML = rooms.map((room, index) => renderRow(room, index, trashed)).join('');
}

function sumAbsences(absences) {
    return Array.isArray(absences)
        ? absences.reduce((sum, a) => sum + (Number(a?.total) || 0), 0)
        : 0;
}

function renderRow(room, index, trashed = false) {
    const examDateFormatted = room.exam_date
        ? new Date(room.exam_date).toLocaleDateString(CONFIG.LOCALE, { day: '2-digit', month: 'short', year: 'numeric' })
        : '<span class="text-neutral-400 italic">Not set</span>';

    const absentTotal = sumAbsences(room.absences);
    const absentBadge = absentTotal > 0
        ? `<span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">${absentTotal} absent</span>`
        : `<span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">All present</span>`;

    const room_no = escapeHtml(room.room ?? 'N/A');
    const floorLabel = escapeHtml(room.floor_label ?? '');
    const major = escapeHtml(room.major ?? 'N/A');
    const degree = escapeHtml(room.degree ?? 'N/A');
    const shift = escapeHtml(room.shift ?? 'N/A');
    const studentTotal = escapeHtml(room.student_total ?? 0);

    return `
        <tr class="block md:table-row bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm md:shadow-none md:border-0 md:border-b md:rounded-none overflow-hidden md:overflow-visible">

            <td class="hidden md:table-cell px-6 py-4">
                ${trashed ? '' : `<input type="checkbox" class="row-select w-4 h-4 rounded border-neutral-300 dark:border-white/20 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer" data-id="${room.id}">`}
            </td>
            <td class="hidden md:table-cell px-6 py-4 text-neutral-400 font-mono text-xs">${index + 1}</td>

            <!-- MOBILE CARD -->
            <td class="block md:hidden p-0">
                <div class="flex items-center justify-between gap-3 p-4 border-b border-neutral-100 dark:border-white/5">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        ${trashed ? '' : `<input type="checkbox" class="row-select shrink-0 w-4 h-4 rounded border-neutral-300 dark:border-white/20 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer" data-id="${room.id}">`}
                        <div class="min-w-0">
                            <div class="font-bold text-neutral-900 dark:text-neutral-100 text-[15px] leading-tight">បន្ទប់ ${room_no}</div>
                            <div class="text-xs text-neutral-400 truncate">${floorLabel} · ${shift}</div>
                        </div>
                    </div>
                    ${absentBadge}
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-2.5 px-4 py-3 text-xs">
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Major</span>
                        <span class="font-semibold text-neutral-800 dark:text-neutral-200">${major}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Degree</span>
                        <span class="font-semibold text-neutral-800 dark:text-neutral-200">${degree}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Students</span>
                        <span class="font-mono text-neutral-600 dark:text-neutral-400">${studentTotal}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Exam Date</span>
                        <span class="font-mono text-neutral-600 dark:text-neutral-400">${examDateFormatted}</span>
                    </div>
                </div>

                <div class="grid ${trashed ? 'grid-cols-1' : 'grid-cols-2'} border-t border-neutral-100 dark:border-white/5">
                    ${trashed ? `
                    <button data-action="restore" data-id="${room.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-emerald-600 active:bg-emerald-50 dark:active:bg-emerald-500/10">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                        </svg>
                        Restore
                    </button>` : `
                    <button data-action="edit" data-id="${room.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-amber-600 border-r border-neutral-100 dark:border-white/5 active:bg-amber-50 dark:active:bg-amber-500/10">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                    <button data-action="delete" data-id="${room.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-rose-600 active:bg-rose-50 dark:active:bg-rose-500/10">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>`}
                </div>
            </td>

            <!-- DESKTOP ROW -->
            <td class="hidden md:table-cell px-6 py-4">
                <div class="flex flex-col gap-0.5">
                    <span class="font-bold text-neutral-900 dark:text-neutral-100 text-sm">បន្ទប់ ${room_no}</span>
                    <span class="text-xs text-neutral-400">${floorLabel}</span>
                </div>
            </td>
            <td class="hidden md:table-cell px-6 py-4 text-sm">${major}</td>
            <td class="hidden md:table-cell px-6 py-4 text-sm">${degree}</td>
            <td class="hidden md:table-cell px-6 py-4 text-sm">${shift}</td>
            <td class="hidden md:table-cell px-6 py-4 font-mono text-sm text-neutral-800 dark:text-neutral-200 font-bold">${studentTotal}</td>
            <td class="hidden md:table-cell px-6 py-4 text-xs font-mono text-neutral-500">${examDateFormatted}</td>
            <td class="hidden md:table-cell px-6 py-4">${absentBadge}</td>
            <td class="hidden md:table-cell p-6 text-right">
                <div class="flex justify-end gap-1.5">
                    ${trashed ? `
                    <button data-action="restore" data-id="${room.id}" class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-xl transition-colors" title="Restore room">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                        </svg>
                    </button>` : `
                    <button data-action="edit" data-id="${room.id}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-xl transition-colors" title="Edit room">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button data-action="delete" data-id="${room.id}" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors" title="Delete room">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>`}
                </div>
            </td>
        </tr>`;
}
