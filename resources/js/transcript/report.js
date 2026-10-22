import { CONFIG } from './config.js';
import { escapeHtml, Toast } from '../invigilator/core.js';

let abortController = null;

/** Opens the report modal and loads it with the current filters. */
export function openReport(dom, ApiService) {
    window.TranscriptReportModal.toggle(true);
    loadReport(dom, ApiService);
}

/** Any filter change reloads; Reset clears them all. */
export function bindReport(dom, ApiService) {
    dom.reportFilters?.addEventListener('change', () => loadReport(dom, ApiService));
    dom.reportReset?.addEventListener('click', () => {
        dom.reportFilters.querySelectorAll('select, input').forEach((el) => (el.value = ''));
        loadReport(dom, ApiService);
    });
}

async function loadReport(dom, ApiService) {
    abortController?.abort();
    abortController = new AbortController();

    const params = new URLSearchParams();
    dom.reportFilters.querySelectorAll('select, input').forEach((el) => el.value && params.set(el.name, el.value));

    const { error, aborted, data } = await ApiService.request(`${CONFIG.TRANSCRIPTS_API}/report?${params}`, {
        signal: abortController.signal,
    });
    if (aborted) return;
    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || 'មិនអាចទាញយករបាយការណ៍បានទេ' });
        return;
    }

    renderReport(dom.reportModal, data);
}

function renderReport(root, data) {
    root.querySelectorAll('[data-report]').forEach((el) => {
        const value = data[el.dataset.report];
        el.textContent = el.dataset.report === 'average_gpa'
            ? (value === null || value === undefined ? '—' : Number(value).toFixed(2))
            : (value ?? 0).toLocaleString();
    });

    renderBars(root.querySelector('[data-report-bars="gpa_bands"]'), data.gpa_bands ?? [], (row) => row.label);
    renderBars(root.querySelector('[data-report-bars="by_degree"]'), data.by_degree ?? [], (row) => row.label);
    renderMajors(root.querySelector('[data-report-table="by_major"]'), data.by_major ?? []);
}

/** One-hue horizontal bars, scaled to the largest row; the count is always printed beside the bar. */
function renderBars(container, rows, labelOf) {
    if (!container) return;
    const max = Math.max(0, ...rows.map((r) => r.total));
    const sum = rows.reduce((n, r) => n + r.total, 0);

    if (!rows.length || max === 0) {
        container.innerHTML = '<p class="py-6 text-xs text-center text-neutral-400">No data for this filter</p>';
        return;
    }

    container.innerHTML = rows.map((row) => {
        const width = (row.total / max) * 100;
        const share = sum ? Math.round((row.total / sum) * 100) : 0;
        const label = escapeHtml(labelOf(row));
        return `<div class="grid grid-cols-[8rem_1fr_4.5rem] items-center gap-3" title="${label}: ${row.total} (${share}%)">
            <span class="text-xs text-neutral-600 dark:text-neutral-300 truncate">${label}</span>
            <div class="h-3 rounded bg-neutral-100 dark:bg-white/5">
                <div class="h-3 rounded bg-indigo-500 dark:bg-indigo-400 transition-[width] duration-500" style="width:${width}%"></div>
            </div>
            <span class="text-xs text-right tabular-nums text-neutral-700 dark:text-neutral-200">
                <b>${row.total}</b> <span class="text-neutral-400">${share}%</span>
            </span>
        </div>`;
    }).join('');
}

function renderMajors(tbody, rows) {
    if (!tbody) return;
    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-xs text-center text-neutral-400">No transcripts for this filter</td></tr>';
        return;
    }

    const cell = 'px-4 py-2.5 text-right tabular-nums';
    tbody.innerHTML = rows.map((row) => `<tr class="text-neutral-700 dark:text-neutral-200">
        <td class="px-4 py-2.5 font-medium text-neutral-900 dark:text-white">${escapeHtml(row.name)}</td>
        <td class="${cell} font-bold">${row.total}</td>
        <td class="${cell}">${row.final}</td>
        <td class="${cell}">${row.draft}</td>
        <td class="${cell} ${row.blocked ? 'text-rose-600 dark:text-rose-400 font-semibold' : ''}">${row.blocked}</td>
        <td class="${cell}">${row.average_gpa === null ? '—' : Number(row.average_gpa).toFixed(2)}</td>
    </tr>`).join('');
}
