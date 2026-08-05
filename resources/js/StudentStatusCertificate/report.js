import { CONFIG } from './config.js';
import { Toast } from './core.js';

const El = {
    modal: () => document.getElementById('certificateReportModal'),
    total: () => document.getElementById('reportTotal'),
    pending: () => document.getElementById('reportPending'),
    printed: () => document.getElementById('reportPrinted'),
    other: () => document.getElementById('reportOther'),
};

export async function handleReportAction(ApiService) {
    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/report`);
    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយករបាយការណ៍បានទេ' });
        return;
    }

    if (El.total()) El.total().textContent = data.total ?? 0;
    if (El.pending()) El.pending().textContent = data.pending ?? 0;
    if (El.printed()) El.printed().textContent = data.printed ?? 0;
    if (El.other()) El.other().textContent = data.other ?? 0;

    El.modal()?.classList.remove('hidden');
    El.modal()?.classList.add('flex');
}

function closeReportModal() {
    El.modal()?.classList.add('hidden');
    El.modal()?.classList.remove('flex');
}

function bindEvents() {
    document.getElementById('closeCertificateReportBtn')?.addEventListener('click', closeReportModal);
    document.getElementById('closeCertificateReportBtn2')?.addEventListener('click', closeReportModal);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindEvents);
} else {
    bindEvents();
}
