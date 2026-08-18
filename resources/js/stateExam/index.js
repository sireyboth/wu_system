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
    bindSortableHeaders(dom, ApiService);
    initEvents(dom, ApiService);
    loadStateExam(dom, ApiService);
});

/**
 * Wires up the <x-ui.data-table> sortable-header buttons (currently just
 * "Room"). Clicking the active column flips asc/desc; clicking a different
 * one switches to it starting at asc. Icon rotation/color reflects state.
 */
function bindSortableHeaders(dom, ApiService) {
    const buttons = document.querySelectorAll('.sortable-th[data-sort-table="state-exam-table-body"]');

    function syncIcons() {
        buttons.forEach((btn) => {
            const icon = btn.querySelector('.sort-icon');
            const isActive = btn.dataset.sortKey === state.sortKey;
            btn.classList.toggle('text-indigo-600', isActive);
            btn.classList.toggle('dark:text-indigo-400', isActive);
            if (icon) {
                icon.classList.toggle('text-indigo-500', isActive);
                icon.classList.toggle('dark:text-indigo-400', isActive);
                icon.classList.toggle('text-neutral-300', !isActive);
                icon.classList.toggle('dark:text-neutral-600', !isActive);
                icon.style.transform = isActive && state.sortDir === 'desc' ? 'rotate(180deg)' : '';
            }
        });
    }

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const key = btn.dataset.sortKey;
            state.sortDir = state.sortKey === key && state.sortDir === 'asc' ? 'desc' : 'asc';
            state.sortKey = key;
            syncIcons();
            loadStateExam(dom, ApiService, dom.searchInput?.value || '');
        });
    });

    syncIcons();
}

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
