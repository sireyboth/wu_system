import { CONFIG } from './config.js';
import { state, Toast } from './core.js';
import { renderTable } from './table-render.js';

/**
 * Loads the student list (with search filtering) so the admin
 * can pick a student and generate a certificate.
 */
export async function loadStudents(dom, ApiService, searchQuery = '') {
    // Abort previous in-flight search requests
    state.searchAbortController?.abort();
    state.searchAbortController = new AbortController();

    const url = `${CONFIG.API_BASE}?search=${encodeURIComponent(searchQuery)}`;
    const { error, aborted, data } = await ApiService.request(url, {
        signal: state.searchAbortController.signal,
    });

    if (aborted) return;
    if (error) {
        Toast.fire({ icon: 'error', title: 'Failed to load students' });
        return;
    }

    const records = data && Array.isArray(data.data)
        ? data.data
        : Array.isArray(data)
            ? data
            : [];

    renderTable(dom, records);
}
