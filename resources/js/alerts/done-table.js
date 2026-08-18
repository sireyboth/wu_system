import { API_BASE, escapeHtml } from './core.js';

const PER_PAGE = 15;
const BODY_ID = 'alert-done-table-body';

function tbody() {
    return document.getElementById(BODY_ID);
}

function paginationContainer() {
    // Shared with <x-ui.data-table>'s built-in slot — only one data-table exists on this page.
    return document.getElementById('pagination-container');
}

function formatDateTime(value) {
    if (!value) return '—';
    const d = new Date(value.replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function emptyRow() {
    return `<tr><td colspan="4" class="px-6 py-10 text-center text-neutral-500">មិនទាន់មានកិច្ចការណាធ្វើរួច (Nothing completed yet)</td></tr>`;
}

function row(alert) {
    return `
        <tr class="block md:table-row bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm md:shadow-none md:border-0 md:border-b md:rounded-none overflow-hidden md:overflow-visible" data-alert-id="${alert.id}">

            <!-- MOBILE CARD -->
            <td class="block md:hidden p-4 space-y-1">
                <div class="font-bold text-neutral-900 dark:text-white text-[15px] leading-tight">${escapeHtml(alert.title)}</div>
                <div class="text-xs text-neutral-400">${escapeHtml(alert.category || '—')} &middot; ${formatDateTime(alert.completed_at)}</div>
                <div class="flex gap-2 pt-2">
                    <button data-action="edit" data-id="${alert.id}" class="flex-1 py-2 text-xs font-bold text-amber-600 bg-amber-50 dark:bg-amber-500/10 rounded-lg">Edit</button>
                    <button data-action="delete" data-id="${alert.id}" class="flex-1 py-2 text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-500/10 rounded-lg">Delete</button>
                </div>
            </td>

            <!-- DESKTOP ROW -->
            <td class="hidden md:table-cell px-6 py-4 font-bold text-sm text-neutral-900 dark:text-white">${escapeHtml(alert.title)}</td>
            <td class="hidden md:table-cell px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300">${escapeHtml(alert.category || '—')}</td>
            <td class="hidden md:table-cell px-6 py-4 text-xs font-mono text-neutral-500">${formatDateTime(alert.completed_at)}</td>
            <td class="hidden md:table-cell px-6 py-4 text-right">
                <div class="flex justify-end gap-1.5">
                    <button data-action="edit" data-id="${alert.id}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-xl transition-colors" title="Edit">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button data-action="delete" data-id="${alert.id}" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors" title="Delete">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>`;
}

function renderPagination(meta, onPageChange) {
    const el = paginationContainer();
    if (!el) return;

    if (!meta || !meta.total) {
        el.innerHTML = '';
        return;
    }

    const { current_page: current, last_page: last, total, from, to } = meta;

    el.innerHTML = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                កំពុងបង្ហាញ ${from ?? 0}–${to ?? 0} នៃ ${total} (Showing ${from ?? 0}-${to ?? 0} of ${total})
            </p>
            <div class="flex items-center gap-1.5">
                <button type="button" data-page="${current - 1}" ${current <= 1 ? 'disabled' : ''}
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-neutral-200 dark:border-white/10 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-white/5 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    មុន (Prev)
                </button>
                <span class="px-2 text-xs font-medium text-neutral-500 dark:text-neutral-400">ទំព័រ ${current} / ${last}</span>
                <button type="button" data-page="${current + 1}" ${current >= last ? 'disabled' : ''}
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-neutral-200 dark:border-white/10 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-white/5 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    បន្ទាប់ (Next)
                </button>
            </div>
        </div>`;

    el.querySelectorAll('button[data-page]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (btn.disabled) return;
            onPageChange(Number(btn.dataset.page));
        });
    });
}

export async function loadDoneTable(page = 1) {
    const body = tbody();
    if (!body) return [];

    body.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-neutral-500">Loading...</td></tr>`;

    const res = await fetch(`${API_BASE}?status=completed&sort=-completed_at&per_page=${PER_PAGE}&page=${page}`, {
        headers: { Accept: 'application/json' },
    });
    const json = await res.json();
    const alerts = Array.isArray(json.data) ? json.data : [];

    body.innerHTML = alerts.length ? alerts.map(row).join('') : emptyRow();
    renderPagination(json.meta, (nextPage) => loadDoneTable(nextPage));

    return alerts;
}
