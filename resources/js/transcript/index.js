import { CONFIG } from './config.js';
import { state } from './state.js';
import { openModal, closeModal, Toast } from '../invigilator/core.js';
import { createApiService } from '../invigilator/api-service.js';
import { renderPagination, bindPagination } from '../invigilator/pagination.js';
import { renderTable } from './table-render.js';
import { openForm, bindCourses, bindKhmerDate, submitForm, handleDelete } from './form.js';

document.addEventListener('DOMContentLoaded', () => {
    const dom = {
        tableBody: document.getElementById('transcript-table-body'),
        loader: document.getElementById('loading-overlay'),
        searchInput: document.getElementById('transcriptSearchInput'),
        addBtn: document.getElementById('transcriptAddBtn'),
        draftBtn: document.getElementById('transcriptDraftBtn'),
        modal: document.getElementById('transcriptModal'),
        modalTitle: document.getElementById('transcriptModalTitle'),
        form: document.getElementById('transcriptForm'),
        courseTemplate: document.getElementById('transcriptCourseTemplate'),
        totalCredits: document.getElementById('transcriptTotalCredits'),
        totalGpa: document.getElementById('transcriptTotalGpa'),
    };
    const ApiService = createApiService(dom);
    const refresh = () => loadTranscripts(dom, ApiService);

    window.TranscriptModal = { toggle: (open) => (open ? openModal(dom.modal) : closeModal(dom.modal)) };

    dom.addBtn?.addEventListener('click', () => openForm(dom));
    bindCourses(dom);
    bindKhmerDate(dom);

    dom.form?.addEventListener('submit', (e) => {
        e.preventDefault();
        submitForm(dom, ApiService, refresh);
    });
    // Draft skips the browser's required-field checks on purpose.
    dom.draftBtn?.addEventListener('click', () => submitForm(dom, ApiService, refresh, { draft: true }));

    dom.tableBody?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn?.dataset.id) return;

        const { action, id } = btn.dataset;
        if (action === 'edit') openForm(dom, id);
        else if (action === 'delete') handleDelete(ApiService, id, refresh);
    });

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

async function loadTranscripts(dom, ApiService) {
    state.searchAbortController?.abort();
    state.searchAbortController = new AbortController();

    const params = new URLSearchParams({
        search: state.search || '',
        page: state.page,
        per_page: CONFIG.PER_PAGE,
    });

    const { error, aborted, data } = await ApiService.request(`${CONFIG.TRANSCRIPTS_API}?${params.toString()}`, {
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
