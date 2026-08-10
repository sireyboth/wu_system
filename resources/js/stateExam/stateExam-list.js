import { CONFIG } from './config.js';
import { state, Toast } from './core.js';
import { renderTable } from './table-render.js';

/**
 * Loads the exam-state (exam room) list, optionally filtered by search,
 * and renders it into the table.
 */
export async function loadStateExam(dom, ApiService, searchQuery = '') {
    state.searchAbortController?.abort();
    state.searchAbortController = new AbortController();

    const url = `${CONFIG.API_BASE}?search=${encodeURIComponent(searchQuery)}`;
    const { error, aborted, data } = await ApiService.request(url, {
        signal: state.searchAbortController.signal,
    });

    if (aborted) return;
    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យបានទេ' });
        return;
    }

    const records = data && Array.isArray(data.data)
        ? data.data
        : Array.isArray(data)
            ? data
            : [];

    renderTable(dom, records);
}
