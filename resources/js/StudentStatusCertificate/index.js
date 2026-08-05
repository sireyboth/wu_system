import { CONFIG } from './config.js';
import { buildDom, state } from './core.js';
import { createApiService } from './api-service.js';
// import { initFormLookups } from './address-cascade.js';
import { handlePrintAction } from './print.js';
import { loadStudents} from './student-form.js';
import { handleEditAction, handleDeleteAction } from './certificate-action.js';
import { handleReportAction } from './report.js';
// resources/js/probisional/index.js
import './student-picker.js';
import './student-status-modal.js';

/**
 * THE single DOMContentLoaded listener for this page.
 * (The original file had two of these binding the same submit/search
 * listeners twice, which was silently double-submitting the form on every
 * save. Don't add a second one here — everything goes through initEvents().)
 */
document.addEventListener('DOMContentLoaded', () => {
    const dom = buildDom();
    const ApiService = createApiService(dom);

    window.reloadCertificateTable = () => loadStudents(dom, ApiService, dom.searchInput?.value || '');

    initEvents(dom, ApiService);
    // initFormLookups(ApiService);
    loadStudents(dom, ApiService);
});

function initEvents(dom, ApiService) {
    document.getElementById('openReportBtn')?.addEventListener('click', () => handleReportAction(ApiService));

    // Search input, debounced
    dom.searchInput?.addEventListener('input', (e) => {
        clearTimeout(state.debounceTimer);
        state.debounceTimer = setTimeout(() => {
            loadStudents(dom, ApiService, e.target.value);
        }, CONFIG.DEBOUNCE_DELAY);
    });

    // Table click event listener: print/edit/delete on each row.
    dom.tableBody?.addEventListener('click', async (e) => {
        const actionBtn = e.target.closest('[data-action]');
        if (!actionBtn) return;

        const action = actionBtn.dataset.action;
        const certId = actionBtn.dataset.id;
        if (!certId) return;

        if (action === 'print') {
            await handlePrintAction(ApiService, certId);
        } else if (action === 'edit') {
            await handleEditAction(ApiService, certId);
        } else if (action === 'delete') {
            await handleDeleteAction(ApiService, certId, () => {
                loadStudents(dom, ApiService, dom.searchInput?.value || '');
            });
        }
    });
}
