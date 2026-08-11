import { CONFIG } from './config.js';
import { state, Toast } from './core.js';
import { renderTable } from './table-render.js';
import { resetBulkSelection } from './stateExam-bulk.js';
import { renderPagination } from './stateExam-pagination.js';

/**
 * Loads the exam-state (exam room) list, optionally filtered by search,
 * and renders it into the table. Pass state.showingTrash = true to list
 * soft-deleted rooms instead (rendered with a Restore button).
 */
export async function loadStateExam(dom, ApiService, searchQuery = '', page = 1) {
    state.searchAbortController?.abort();
    state.searchAbortController = new AbortController();

    const trashedParam = state.showingTrash ? '&trashed=1' : '';
    const url = `${CONFIG.API_BASE}?search=${encodeURIComponent(searchQuery)}${trashedParam}&page=${page}`;
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

    renderTable(dom, records, state.showingTrash);
    resetBulkSelection(dom);
    renderPagination(data?.meta);
}
