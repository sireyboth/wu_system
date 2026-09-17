import { CONFIG } from './config.js';
import { buildDom, state, Toast } from './core.js';
import { createApiService } from './api-service.js';
import { loadStateExam } from './stateExam-list.js';
import { handleEditAction, handleDeleteAction, handleRestoreAction, handleFormSubmit, openStateExamModal, closeStateExamModal } from './stateExam-action.js';
import { bindMajorsEvents, resetMajorsRows } from './stateExam-majors.js';
import { resetAbsenceInputs } from './stateExam-absences.js';
import { bindBulkSelect } from './stateExam-bulk.js';
import { bindPagination } from './stateExam-pagination.js';
import { loadExamTermLookups, bindExamTermEvents, applySelectedTermSlots, openExamTermModal, closeExamTermModal } from './exam-term.js';

document.addEventListener('DOMContentLoaded', async () => {
    const dom = buildDom();
    const ApiService = createApiService(dom);

    window.AppModal = {
        toggle: (open) => (open ? openStateExamModal(dom) : closeStateExamModal(dom)),
    };
    window.ExamTermModal = {
        toggle: (open) => (open ? openExamTermModal(dom) : closeExamTermModal(dom)),
    };

    bindMajorsEvents(dom);
    resetMajorsRows(dom);
    resetAbsenceInputs(dom);
    bindBulkSelect(dom, ApiService, () => loadStateExam(dom, ApiService, dom.searchInput?.value || ''));
    bindPagination((page) => loadStateExam(dom, ApiService, dom.searchInput?.value || '', page));
    bindSortableHeaders(dom, ApiService);
    bindExamTermEvents(dom, ApiService);
    initEvents(dom, ApiService);
    initExportImportEvents(dom, ApiService);

    await loadExamTermLookups(dom, ApiService);
    applySelectedTermSlots(dom);
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

    dom.termFilterSelect?.addEventListener('change', (e) => {
        state.termFilterId = e.target.value;
        loadStateExam(dom, ApiService, dom.searchInput?.value || '');
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

/**
 * Export always mirrors the current grid (term filter + search). Import
 * is scoped to a single term — the room-creation modal already requires
 * one, so re-using state.termFilterId here keeps the same mental model
 * (pick a term first, then bulk-load rooms into it).
 */
function initExportImportEvents(dom, ApiService) {
    dom.exportBtn?.addEventListener('click', () => {
        const params = new URLSearchParams();
        if (state.termFilterId) params.set('exam_term_id', state.termFilterId);
        if (dom.searchInput?.value) params.set('search', dom.searchInput.value);
        if (state.showingTrash) params.set('trashed', '1');

        window.open(`${CONFIG.EXAM_STATES_EXPORT_API}?${params.toString()}`, '_blank');
    });

    dom.importBtn?.addEventListener('click', () => {
        if (!state.termFilterId) {
            Toast.fire({ icon: 'warning', title: 'Select an Exam Term above first — import loads rooms into that term.' });
            return;
        }
        dom.importFileInput?.click();
    });

    dom.importFileInput?.addEventListener('change', async () => {
        const file = dom.importFileInput.files?.[0];
        dom.importFileInput.value = '';
        if (!file || !state.termFilterId) return;

        const body = new FormData();
        body.append('file', file);
        body.append('exam_term_id', state.termFilterId);

        const { error, data } = await ApiService.request(CONFIG.EXAM_STATES_IMPORT_API, { method: 'POST', body });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Import failed.' });
            return;
        }

        const report = data?.data?.report ?? {};
        const skippedCount = report.skipped?.length ?? 0;
        Toast.fire({
            icon: skippedCount ? 'warning' : 'success',
            title: `${report.created_count ?? 0} created, ${report.updated_count ?? 0} updated${skippedCount ? `, ${skippedCount} row(s) skipped` : ''}.`,
        });
        if (skippedCount) {
            console.warn('Exam room import — skipped rows:', report.skipped);
        }

        loadStateExam(dom, ApiService, dom.searchInput?.value || '');
    });
}
