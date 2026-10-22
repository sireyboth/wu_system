import { CONFIG } from './config.js';
import { buildDom, state, openModal, closeModal, Toast } from './core.js';
import { createApiService } from './api-service.js';
import { renderTable } from './table-render.js';
import { renderPagination, bindPagination } from './pagination.js';
import { openForm, addHistoryRow, bindHistoryList, bindPhotoPicker, submitForm, handleDelete } from './form.js';
import { openQr, downloadQr, printCard } from './qr.js';

document.addEventListener('DOMContentLoaded', () => {
    const dom = buildDom();
    const ApiService = createApiService(dom);

    const refresh = () => loadInvigilators(dom, ApiService);

    window.InvigilatorModal = { toggle: (open) => (open ? openModal(dom.modal) : closeModal(dom.modal)) };
    window.InvigilatorQrModal = { toggle: (open) => (open ? openModal(dom.qrModal) : closeModal(dom.qrModal)) };

    dom.addBtn?.addEventListener('click', () => openForm(dom));
    dom.addHistoryBtn?.addEventListener('click', () => {
        addHistoryRow(dom).querySelector('[data-field="description"]')?.focus();
    });
    bindHistoryList(dom);
    bindPhotoPicker(dom);

    dom.form?.addEventListener('submit', (e) => {
        e.preventDefault();
        submitForm(dom, ApiService, refresh);
    });

    dom.tableBody?.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action]');
        if (!btn?.dataset.id) return;

        const { action, id } = btn.dataset;
        if (action === 'edit') openForm(dom, id);
        else if (action === 'delete') handleDelete(ApiService, id, refresh);
        else if (action === 'qr') openQr(dom, id);
    });

    dom.qrDownloadBtn?.addEventListener('click', downloadQr);
    dom.qrPrintBtn?.addEventListener('click', printCard);

    dom.searchInput?.addEventListener('input', (e) => {
        clearTimeout(state.debounceTimer);
        state.debounceTimer = setTimeout(() => {
            state.search = e.target.value;
            state.page = 1;
            refresh();
        }, CONFIG.DEBOUNCE_DELAY);
    });

    bindPagination((page) => {
        state.page = page;
        refresh();
    });

    refresh();
});

async function loadInvigilators(dom, ApiService) {
    state.searchAbortController?.abort();
    state.searchAbortController = new AbortController();

    const params = new URLSearchParams({
        search: state.search || '',
        page: state.page,
        per_page: CONFIG.PER_PAGE,
    });

    const { error, aborted, data } = await ApiService.request(`${CONFIG.INVIGILATORS_API}?${params.toString()}`, {
        signal: state.searchAbortController.signal,
    });

    if (aborted) return;
    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យបានទេ' });
        return;
    }

    renderTable(dom, Array.isArray(data?.data) ? data.data : [], data?.meta);
    renderPagination(data?.meta);
}
