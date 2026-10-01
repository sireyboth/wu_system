import { state, escapeHtml } from './core.js';

let lastRows = new Map();

export function getRenderedRow(id) {
    return lastRows.get(String(id));
}

export function renderTable(dom, rows, meta) {
    if (!dom.tableBody) return;

    lastRows = new Map((rows ?? []).map((r) => [String(r.id), r]));

    if (!rows || rows.length === 0) {
        dom.tableBody.className = 'divide-y divide-neutral-200 dark:divide-white/5';
        dom.tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-10 text-neutral-500">
            រកមិនឃើញអ្នកឃ្លាំមើលទេ (No invigilators found).
        </td></tr>`;
        return;
    }

    dom.tableBody.className =
        'grid grid-cols-1 gap-3 p-4 md:p-0 md:table-row-group md:gap-0 md:divide-y md:divide-neutral-200 md:dark:divide-white/5';

    const offset = meta?.from ? meta.from - 1 : 0;
    dom.tableBody.innerHTML = rows.map((row, i) => renderRow(row, offset + i + 1)).join('');
}

function stars(rating) {
    if (!rating) return '';
    return `<span class="text-amber-400">${'★'.repeat(rating)}</span><span class="text-neutral-300 dark:text-neutral-600">${'★'.repeat(5 - rating)}</span>`;
}

function renderRow(row, number) {
    const nameKh = escapeHtml(row.name_kh);
    const nameEn = escapeHtml(row.name_en);
    const code = escapeHtml(row.code);
    const batch = escapeHtml(row.batch || '—');
    const histories = Array.isArray(row.histories) ? row.histories : [];
    const latest = histories[0];
    const avatar = row.photo_url
        ? `<img src="${escapeHtml(row.photo_url)}" alt="" loading="lazy" class="w-10 h-10 shrink-0 rounded-xl object-cover border border-neutral-200 dark:border-white/10">`
        : `<div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold">${escapeHtml((row.name_en || row.name_kh || '?').charAt(0))}</div>`;

    const historyCell = histories.length
        ? `<div class="flex flex-col gap-0.5">
               <span class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">${histories.length} record(s)</span>
               <span class="text-xs">${stars(latest.rating)}</span>
           </div>`
        : '<span class="text-xs text-neutral-300 dark:text-neutral-600">—</span>';

    const btn = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-colors';
    const actions = [
        `<button data-action="qr" data-id="${row.id}" class="${btn} text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20">QR</button>`,
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
                <div class="flex items-center gap-3">
                ${avatar}
                <div>
                    <div class="font-bold text-neutral-900 dark:text-neutral-100 text-[15px] leading-tight">${nameKh}</div>
                    <div class="text-sm text-neutral-500">${nameEn}</div>
                    <div class="text-xs font-mono text-neutral-400 mt-0.5">${code} · ${batch}</div>
                </div>
                </div>
                ${historyCell}
                <div class="flex flex-wrap gap-2">${actions}</div>
            </td>

            <!-- DESKTOP ROW -->
            <td class="hidden md:table-cell px-6 py-4 font-mono text-xs text-neutral-700 dark:text-neutral-300">${code}</td>
            <td class="hidden md:table-cell px-6 py-4">
                <div class="flex items-center gap-3">
                    ${avatar}
                    <div class="flex flex-col gap-0.5">
                        <span class="font-bold text-neutral-900 dark:text-neutral-100 text-sm">${nameKh}</span>
                        <span class="text-xs text-neutral-400">${nameEn}</span>
                    </div>
                </div>
            </td>
            <td class="hidden md:table-cell px-6 py-4 text-sm">${batch}</td>
            <td class="hidden md:table-cell px-6 py-4">${historyCell}</td>
            <td class="hidden md:table-cell px-6 py-4 text-right"><div class="inline-flex gap-2">${actions}</div></td>
        </tr>`;
}
