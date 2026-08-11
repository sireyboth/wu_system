import { CONFIG } from './config.js';
import { Toast } from './core.js';

const BODY_ID = 'state-exam-table-body';
let selectedIds = new Set();

function els() {
    return {
        selectAll: document.getElementById(`${BODY_ID}-select-all`),
        bulkBar: document.getElementById(`${BODY_ID}-bulk-bar`),
        bulkCount: document.getElementById(`${BODY_ID}-bulk-count`),
        bulkDeleteBtn: document.getElementById(`${BODY_ID}-bulk-delete-btn`),
    };
}

function updateBulkBar(dom) {
    const { bulkBar, bulkCount, selectAll } = els();
    const count = selectedIds.size;

    bulkBar?.classList.toggle('hidden', count === 0);
    bulkBar?.classList.toggle('flex', count > 0);
    if (bulkCount) bulkCount.textContent = count;

    if (selectAll) {
        const checkboxes = dom.tableBody?.querySelectorAll('.row-select') ?? [];
        const total = checkboxes.length;
        selectAll.checked = total > 0 && count === total;
        selectAll.indeterminate = count > 0 && count < total;
    }
}

/** Call after every table (re)render — fresh rows means no stale checked state. */
export function resetBulkSelection(dom) {
    selectedIds = new Set();
    updateBulkBar(dom);
}

/** Call once on page init. `onDeleted` is invoked after a successful bulk delete (reload the list). */
export function bindBulkSelect(dom, ApiService, onDeleted) {
    const { selectAll, bulkDeleteBtn } = els();

    selectAll?.addEventListener('change', () => {
        const checkboxes = dom.tableBody?.querySelectorAll('.row-select') ?? [];
        checkboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
            if (selectAll.checked) selectedIds.add(checkbox.dataset.id);
            else selectedIds.delete(checkbox.dataset.id);
        });
        updateBulkBar(dom);
    });

    // Delegated — survives tbody innerHTML swaps on every reload/search.
    dom.tableBody?.addEventListener('change', (e) => {
        const checkbox = e.target.closest('.row-select');
        if (!checkbox) return;

        if (checkbox.checked) selectedIds.add(checkbox.dataset.id);
        else selectedIds.delete(checkbox.dataset.id);
        updateBulkBar(dom);
    });

    bulkDeleteBtn?.addEventListener('click', async () => {
        if (selectedIds.size === 0) return;

        const confirmation = await Swal.fire({
            title: `លុបទាំង ${selectedIds.size} បន្ទប់?`,
            text: 'ទិន្នន័យនេះមិនអាចយកមកវិញបានឡើយ!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'បាទ/ចាស លុបវា!',
            cancelButtonText: 'បោះបង់',
        });

        if (!confirmation.isConfirmed) return;

        const { error } = await ApiService.request(`${CONFIG.API_BASE}/bulk`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: Array.from(selectedIds) }),
        });

        if (!error) {
            Toast.fire({ icon: 'success', title: 'លុបទិន្នន័យបានជោគជ័យ!' });
            resetBulkSelection(dom);
            onDeleted?.();
        } else {
            Toast.fire({ icon: 'error', title: 'មានបញ្ហាមិនអាចលុបទិន្នន័យនេះបាន' });
        }
    });
}
