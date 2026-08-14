import { CONFIG } from './config.js';
import { buildDom, state } from './core.js';
import { createApiService } from './api-service.js';
import { loadStateExam } from './stateExam-list.js';
import { handleEditAction, handleDeleteAction, handleRestoreAction, handleFormSubmit, openStateExamModal, closeStateExamModal } from './stateExam-action.js';
import { bindMajorsEvents, resetMajorsRows } from './stateExam-majors.js';
import { resetAbsenceInputs } from './stateExam-absences.js';
import { bindBulkSelect } from './stateExam-bulk.js';
import { bindPagination } from './stateExam-pagination.js';

document.addEventListener('DOMContentLoaded', () => {
    const dom = buildDom();
    const ApiService = createApiService(dom);

    window.AppModal = {
        toggle: (open) => (open ? openStateExamModal(dom) : closeStateExamModal(dom)),
    };

    bindMajorsEvents(dom);
    resetMajorsRows(dom);
    resetAbsenceInputs(dom);
    bindBulkSelect(dom, ApiService, () => loadStateExam(dom, ApiService, dom.searchInput?.value || ''));
    bindPagination((page) => loadStateExam(dom, ApiService, dom.searchInput?.value || '', page));
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

    // Table click event listener: edit/delete/restore on each row.
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
        } else if (action === 'restore') {
            await handleRestoreAction(dom, ApiService, id);
        }
    });

    // Trash toggle
    const toggleTrashBtn = document.getElementById('toggleTrashBtn');
    const toggleTrashLabel = document.getElementById('toggleTrashLabel');
    const createRoomBtn = document.getElementById('createRoomBtn');

    toggleTrashBtn?.addEventListener('click', () => {
        state.showingTrash = !state.showingTrash;

        toggleTrashBtn.classList.toggle('bg-neutral-900', state.showingTrash);
        toggleTrashBtn.classList.toggle('text-white', state.showingTrash);
        toggleTrashBtn.classList.toggle('dark:bg-white', state.showingTrash);
        toggleTrashBtn.classList.toggle('dark:text-neutral-900', state.showingTrash);
        if (toggleTrashLabel) {
            toggleTrashLabel.textContent = state.showingTrash
                ? 'ត្រឡប់ក្រោយ (Back to list)'
                : 'ធុងសំរាម (Trash)';
        }
        if (createRoomBtn) createRoomBtn.classList.toggle('hidden', state.showingTrash);

        loadStateExam(dom, ApiService, dom.searchInput?.value || '');
    });
}
