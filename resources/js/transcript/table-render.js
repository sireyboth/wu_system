import { escapeHtml } from '../invigilator/core.js';
import { state } from './state.js';

let lastRows = new Map();

export function getRenderedRow(id) {
    return lastRows.get(String(id));
}

export function renderTable(dom, rows, meta) {
    if (!dom.tableBody) return;

    lastRows = new Map((rows ?? []).map((r) => [String(r.id), r]));

    if (!rows || rows.length === 0) {
        dom.tableBody.className = 'divide-y divide-neutral-200 dark:divide-white/5';
        dom.tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-neutral-500">
            រកមិនឃើញឯកសារទេ (No documents found).
        </td></tr>`;
        return;
    }

    dom.tableBody.className =
        'grid grid-cols-1 gap-3 p-4 md:p-0 md:table-row-group md:gap-0 md:divide-y md:divide-neutral-200 md:dark:divide-white/5';

    const offset = meta?.from ? meta.from - 1 : 0;
    dom.tableBody.innerHTML = rows.map((row, i) => renderRow(row, offset + i + 1)).join('');
}

function renderRow(row, number) {
    const draftBadge = row.is_draft
        ? ' <span class="inline-flex ms-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Draft</span>'
        : '';
    const fullName = escapeHtml(`${row.surname ?? ''} ${row.name ?? ''}`.trim() || '(no name yet)') + draftBadge;
    const issueNo = escapeHtml(row.issue_no || '—');
    const typeBadge = row.doc_type === 'provisional'
        ? '<span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-400">Provisional</span>'
        : '<span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-violet-100 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400">Transcript</span>';
    const code = escapeHtml(row.student_code || '—');
    const major = escapeHtml(row.major?.name_en || '—');
    const year = escapeHtml(row.academic_year || '');
    const totals = `<div class="flex flex-col gap-0.5">
            <span class="text-sm font-bold text-neutral-900 dark:text-white">${Number(row.total_gpa ?? 0).toFixed(2)}</span>
            <span class="text-xs text-neutral-400">${row.total_credits ?? 0} credits</span>
        </div>`;
    const entrance = row.entrance_exam
        ? '<span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Passed</span>'
        : '<span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">Failed</span>';

    const btn = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-colors';
    const blockedReason = row.is_draft
        ? 'Draft — open with Edit, finish it and press Save to print'
        : 'Entrance exam not passed — cannot print';
    const printBtn = row.can_print
        ? `<a href="${escapeHtml(row.print_url)}" target="_blank" rel="noopener" class="${btn} text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20">បោះពុម្ព (Print)</a>`
        : `<span title="${blockedReason}" class="${btn} cursor-not-allowed text-neutral-400 bg-neutral-100 dark:bg-white/5">បោះពុម្ព (Print)</span>`;
    const actions = [
        printBtn,
        state.permissions.edit
            ? `<button data-action="edit" data-id="${row.id}" class="${btn} text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20">កែ (Edit)</button>`
            : '',
        state.permissions.delete
            ? `<button data-action="delete" data-id="${row.id}" class="${btn} text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20">លុប (Delete)</button>`
            : '',
    ].join('');

    return `
        <tr class="block md:table-row bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm md:shadow-none md:border-0 md:border-b md:rounded-none overflow-hidden md:overflow-visible">
            <td class="hidden md:table-cell px-6 py-4 text-neutral-400 font-mono text-xs">${number}</td>

            <!-- MOBILE CARD -->
            <td class="block md:hidden p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-bold text-neutral-900 dark:text-neutral-100 text-[15px] leading-tight">${fullName}</div>
                        <div class="text-xs font-mono text-neutral-400 mt-0.5">${issueNo} · ID ${code}</div>
                        <div class="mt-1">${typeBadge}</div>
                        <div class="text-xs text-neutral-500 mt-0.5">${major} ${year ? `· ${year}` : ''}</div>
                    </div>
                    ${entrance}
                </div>
                ${totals}
                <div class="flex flex-wrap gap-2">${actions}</div>
            </td>

            <!-- DESKTOP ROW -->
            <td class="hidden md:table-cell px-6 py-4">
                <div class="flex flex-col items-start gap-1">
                    <span class="font-mono text-xs text-neutral-700 dark:text-neutral-300">${issueNo}</span>
                    ${typeBadge}
                </div>
            </td>
            <td class="hidden md:table-cell px-6 py-4">
                <div class="flex flex-col gap-0.5">
                    <span class="font-bold text-neutral-900 dark:text-neutral-100 text-sm">${fullName}</span>
                    <span class="text-xs font-mono text-neutral-400">ID ${code}</span>
                </div>
            </td>
            <td class="hidden md:table-cell px-6 py-4">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm">${major}</span>
                    <span class="text-xs text-neutral-400">${year}</span>
                </div>
            </td>
            <td class="hidden md:table-cell px-6 py-4">${totals}</td>
            <td class="hidden md:table-cell px-6 py-4">${entrance}</td>
            <td class="hidden md:table-cell px-6 py-4 text-right"><div class="inline-flex gap-2">${actions}</div></td>
        </tr>`;
}
