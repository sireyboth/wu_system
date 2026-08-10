// import { CONFIG } from './config.js';
// import { Toast } from './core.js';

// const El = {
//     modal: () => document.getElementById('certificateReportModal'),
//     total: () => document.getElementById('reportTotal'),
//     pending: () => document.getElementById('reportPending'),
//     printed: () => document.getElementById('reportPrinted'),
//     other: () => document.getElementById('reportOther'),
//     ring: () => document.getElementById('reportRing'),
//     ringPercent: () => document.getElementById('reportPrintedPercent'),
// };

// export async function handleReportAction(ApiService) {
//     const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/report`);
//     if (error) {
//         Toast.fire({ icon: 'error', title: 'មិនអាចទាញយករបាយការណ៍បានទេ' });
//         return;
//     }

//     const total = data.total ?? 0;
//     const printed = data.printed ?? 0;
//     const percent = total > 0 ? Math.round((printed / total) * 100) : 0;

//     if (El.total()) El.total().textContent = total;
//     if (El.pending()) El.pending().textContent = data.pending ?? 0;
//     if (El.printed()) El.printed().textContent = printed;
//     if (El.other()) El.other().textContent = data.other ?? 0;
//     if (El.ringPercent()) El.ringPercent().textContent = `${percent}%`;
//     if (El.ring()) {
//         El.ring().style.background =
//             `conic-gradient(#6366f1 ${percent * 3.6}deg, #e5e7eb ${percent * 3.6}deg)`;
//     }

//     El.modal()?.classList.remove('hidden');
//     El.modal()?.classList.add('flex');
// }

// function closeReportModal() {
//     El.modal()?.classList.add('hidden');
//     El.modal()?.classList.remove('flex');
// }

// function bindEvents() {
//     document.getElementById('closeCertificateReportBtn')?.addEventListener('click', closeReportModal);
//     document.getElementById('closeCertificateReportBtn2')?.addEventListener('click', closeReportModal);
// }

// if (document.readyState === 'loading') {
//     document.addEventListener('DOMContentLoaded', bindEvents);
// } else {
//     bindEvents();
// }
