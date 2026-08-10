import { CONFIG } from './config.js';
import { buildDom, state } from './core.js';
import { createApiService } from './api-service.js';
import { loadStateExam } from './stateExam-list.js';
import { handleEditAction, handleDeleteAction, handleFormSubmit, openStateExamModal, closeStateExamModal } from './stateExam-action.js';
import { bindMajorsEvents, resetMajorsRows } from './stateExam-majors.js';

document.addEventListener('DOMContentLoaded', () => {
    const dom = buildDom();
    const ApiService = createApiService(dom);

    window.AppModal = {
        toggle: (open) => (open ? openStateExamModal(dom) : closeStateExamModal(dom)),
    };

    bindMajorsEvents(dom);
    resetMajorsRows(dom);
    initEvents(dom, ApiService);
    loadStateExam(dom, ApiService);
});

function initEvents(dom, ApiService) {
    dom.form?.addEventListener('submit', (e) => handleFormSubmit(dom, ApiService, e));

    // Search input, debounced
    dom.searchInput?.addEventListener('input', (e) => {
        clearTimeout(state.debounceTimer);
        state.debounceTimer = setTimeout(() => {
            loadStateExam(dom, ApiService, e.target.value);
        }, CONFIG.DEBOUNCE_DELAY);
    });

    // Table click event listener: edit/delete on each row.
    dom.tableBody?.addEventListener('click', async (e) => {
        const actionBtn = e.target.closest('[data-action]');
        if (!actionBtn) return;

        const action = actionBtn.dataset.action;
        const id = actionBtn.dataset.id;
        if (!id) return;

        if (action === 'edit') {
            await handleEditAction(dom, ApiService, id);
        } else if (action === 'delete') {
            await handleDeleteAction(dom, ApiService, id);
        }
    });
}
