/**
 * Registrar's attendance review queue — flagged scans (risk-scored,
 * never blocked at scan time) and pending corrections (a lecturer asking
 * to change a locked record). See AttendanceReviewController.
 */
(() => {
    'use strict';

    const CONFIG = { API_BASE: '/api/v1/attendance-review' };

    const Toast = typeof Swal !== 'undefined' ? Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    }) : { fire: console.log };

    const DOM = {
        flaggedTableBody: document.getElementById('flaggedTableBody'),
        flaggedCount: document.getElementById('flaggedCount'),
        correctionsTableBody: document.getElementById('correctionsTableBody'),
        correctionsCount: document.getElementById('correctionsCount'),
    };

    async function request(url, options = {}) {
        try {
            const { headers, method = 'GET', body, ...rest } = options;
            const response = await fetch(url, {
                method, credentials: 'same-origin',
                headers: { Accept: 'application/json', ...headers },
                body, ...rest,
            });
            const contentType = response.headers.get('content-type');
            const isJson = contentType && contentType.includes('application/json');
            const result = isJson ? await response.json() : null;
            return { error: !response.ok, status: response.status, data: result };
        } catch (err) {
            console.error(`[API Error] ${url}:`, err);
            return { error: true, status: 500, data: null };
        }
    }

    function riskBadge(score) {
        const color = score >= 70 ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'
            : score >= 40 ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'
            : 'bg-neutral-100 text-neutral-600 dark:bg-white/5 dark:text-neutral-400';
        return `<span class="px-2.5 py-1 rounded-full text-xs font-bold ${color}">${score}</span>`;
    }

    function signalsSummary(signals) {
        const parts = [];
        if (signals?.device_shared_with_student_ids?.length) {
            parts.push(`Device also used for ${signals.device_shared_with_student_ids.length} other student(s)`);
        }
        if (signals?.rapid_successive_scan_seconds !== undefined) {
            parts.push(`Scanned ${signals.rapid_successive_scan_seconds}s after another student, same device`);
        }
        if (signals?.device_first_seen) {
            parts.push('First time this device has been seen');
        }
        return parts.length ? parts.join(' · ') : '—';
    }

    async function loadQueue() {
        const { error, data } = await request(`${CONFIG.API_BASE}/`);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load review queue.' });
            return;
        }

        renderFlagged(data.data.flagged_scans);
        renderCorrections(data.data.pending_corrections);
    }

    function renderFlagged(page) {
        const rows = page?.data ?? [];
        DOM.flaggedCount.textContent = `${page?.total ?? rows.length} total`;

        if (!rows.length) {
            DOM.flaggedTableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-neutral-400">Nothing flagged — every scan looked clean.</td></tr>';
            return;
        }

        DOM.flaggedTableBody.innerHTML = rows.map((row) => `
            <tr class="hover:bg-neutral-50/70 dark:hover:bg-white/[0.02]">
                <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">${row.student}</td>
                <td class="px-6 py-4">${row.class}</td>
                <td class="px-6 py-4">${riskBadge(row.risk_score)}</td>
                <td class="px-6 py-4 text-xs max-w-xs">${signalsSummary(row.signals)}</td>
                <td class="px-6 py-4 text-xs font-mono">${row.marked_at ?? '—'}</td>
                <td class="px-6 py-4 text-right">
                    <button data-action="dismiss-flag" data-id="${row.id}" class="px-3 py-1.5 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-lg">Dismiss</button>
                </td>
            </tr>`).join('');
    }

    function renderCorrections(page) {
        const rows = page?.data ?? [];
        DOM.correctionsCount.textContent = `${page?.total ?? rows.length} total`;

        if (!rows.length) {
            DOM.correctionsTableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-neutral-400">No pending correction requests.</td></tr>';
            return;
        }

        DOM.correctionsTableBody.innerHTML = rows.map((row) => `
            <tr class="hover:bg-neutral-50/70 dark:hover:bg-white/[0.02]">
                <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">${row.student}</td>
                <td class="px-6 py-4">${row.class}</td>
                <td class="px-6 py-4 text-xs uppercase font-semibold">
                    <span class="text-neutral-400">${row.old_status}</span> → <span class="text-indigo-600 dark:text-indigo-400">${row.new_status}</span>
                </td>
                <td class="px-6 py-4 text-xs max-w-xs">${row.reason}</td>
                <td class="px-6 py-4 text-xs">${row.requested_by ?? '—'}</td>
                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                    <button data-action="approve" data-id="${row.id}" class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">Approve</button>
                    <button data-action="reject" data-id="${row.id}" class="px-3 py-1.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg">Reject</button>
                </td>
            </tr>`).join('');
    }

    async function dismissFlag(id) {
        const { error, data } = await request(`${CONFIG.API_BASE}/verifications/${id}/review`, { method: 'PATCH' });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to dismiss.' });
            return;
        }
        Toast.fire({ icon: 'success', title: 'Marked reviewed.' });
        loadQueue();
    }

    async function decideCorrection(id, decision) {
        const confirmation = await Swal.fire({
            title: decision === 'approve' ? 'Approve this correction?' : 'Reject this correction?',
            text: decision === 'approve' ? 'This will update the actual attendance record.' : 'The record stays as it is.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: decision === 'approve' ? '#059669' : '#e11d48',
            confirmButtonText: decision === 'approve' ? 'Approve' : 'Reject',
        });
        if (!confirmation.isConfirmed) return;

        const { error, data } = await request(`${CONFIG.API_BASE}/corrections/${id}/decide`, {
            method: 'PATCH', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ decision }),
        });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to decide.' });
            return;
        }
        Toast.fire({ icon: 'success', title: data?.message || 'Done.' });
        loadQueue();
    }

    DOM.flaggedTableBody?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action="dismiss-flag"]');
        if (!btn) return;
        dismissFlag(btn.dataset.id);
    });

    DOM.correctionsTableBody?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        if (btn.dataset.action === 'approve') decideCorrection(btn.dataset.id, 'approve');
        if (btn.dataset.action === 'reject') decideCorrection(btn.dataset.id, 'reject');
    });

    document.addEventListener('DOMContentLoaded', loadQueue);
})();
