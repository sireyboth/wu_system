import { CONFIG } from './config.js';
import { state, Toast } from './core.js';
import { getRenderedRow } from './table-render.js';
import { compressImage } from './image-compress.js';
import { studentName as studentDisplayName } from '../uitilities/helper.js';

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

/**
 * Opens the Mark Paid modal for one or more registration ids. All ids
 * must belong to the same student — one payment_batch (one invoice)
 * covers one student's subjects, never a mix. Caller is responsible for
 * that invariant (see index.js's bulk-selection guard).
 */
export function openPayModal(dom, ids) {
    const rows = ids.map((id) => getRenderedRow(id)).filter(Boolean);
    if (rows.length === 0) return;

    state.payingIds = ids;
    state.payingStudentId = rows[0].student?.id ?? null;
    state.editingPaymentBatchId = null;
    setPayModalMode(dom, false);

    const studentName = escapeHtml(studentDisplayName(rows[0].student) || '—');
    const subjectList = rows.map((r) => escapeHtml(r.subject?.name || r.subject?.code || '—')).join(', ');

    if (dom.payContext) {
        dom.payContext.innerHTML = `<strong>${studentName}</strong><br><span class="text-xs text-neutral-500 dark:text-neutral-400">${rows.length} subject(s): ${subjectList}</span>`;
    }
    if (dom.payRemark) dom.payRemark.value = '';
    clearPayFile(dom);

    window.RetakePayModal.toggle(true);
}

/**
 * Stages an image (from file picker, drag-drop, or clipboard paste) for
 * upload — compressed first (see image-compress.js) so what's staged for
 * preview is exactly what gets uploaded, not the raw multi-MB original.
 */
export async function setPayFile(dom, file) {
    if (!file || !file.type.startsWith('image/')) return;

    const compressed = await compressImage(file);
    state.payingFile = compressed;

    const url = URL.createObjectURL(compressed);
    if (dom.payPreview) {
        dom.payPreview.src = url;
        dom.payPreview.classList.remove('hidden');
    }
    if (dom.payFileName) {
        const kb = Math.round(compressed.size / 1024);
        dom.payFileName.textContent = `${compressed.name || 'pasted-image'} (${kb} KB)`;
    }
    if (dom.payClearBtn) dom.payClearBtn.classList.remove('hidden');
}

export function clearPayFile(dom) {
    state.payingFile = null;
    dom.payPasteHint?.classList.add('hidden');
    if (dom.payFileInput) dom.payFileInput.value = '';
    if (dom.payPreview) {
        dom.payPreview.src = '';
        dom.payPreview.classList.add('hidden');
    }
    if (dom.payFileName) dom.payFileName.textContent = '';
    if (dom.payClearBtn) dom.payClearBtn.classList.add('hidden');
}

/**
 * Submits the Mark Paid modal — creates a new PaymentBatch for the
 * student (multipart, since it may carry an image), then marks every id
 * in state.payingIds paid against it in one bulk-mark-paid call (works
 * the same whether it's 1 id or several).
 */
export async function submitPayForm(dom, ApiService, onDone) {
    if (state.editingPaymentBatchId) {
        dom.paySubmitBtn && (dom.paySubmitBtn.disabled = true);
        try {
            await submitEditPayForm(dom, ApiService, onDone);
        } finally {
            dom.paySubmitBtn && (dom.paySubmitBtn.disabled = false);
        }
        return;
    }
    if (!state.payingStudentId || state.payingIds.length === 0) return;

    const body = new FormData();
    body.append('student_id', state.payingStudentId);
    if (state.payingFile) body.append('invoice_file', state.payingFile);
    if (dom.payRemark?.value) body.append('remark', dom.payRemark.value);

    const { error: batchError, data: batchData } = await ApiService.request(CONFIG.PAYMENT_BATCHES_API, {
        method: 'POST',
        body,
    });

    if (batchError) {
        const firstError = batchData?.errors ? Object.values(batchData.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || batchData?.message || 'មិនអាចបង្កើតវិក័យបត្របានទេ' });
        return;
    }

    const paymentBatchId = batchData?.data?.id;

    const { error, data } = await ApiService.request(`${CONFIG.REGISTRATIONS_API}/bulk-mark-paid`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            payment_batch_id: paymentBatchId,
            ids: state.payingIds,
        }),
    });

    if (error) {
        Toast.fire({ icon: 'error', title: data?.message || 'មិនអាចកត់ត្រាការបង់ប្រាក់បានទេ' });
        return;
    }

    Toast.fire({ icon: 'success', title: data?.message || 'កត់ត្រាការបង់ប្រាក់ជោគជ័យ!' });
    window.RetakePayModal.toggle(false);
    state.payingIds = [];
    state.payingStudentId = null;
    state.selectedIds.clear();
    clearPayFile(dom);
    onDone();
}

function setPayModalMode(dom, isEdit) {
    if (dom.payModalTitle) {
        dom.payModalTitle.textContent = isEdit ? 'កែប្រែការបង់ប្រាក់ (Edit Payment)' : 'កត់ត្រាការបង់ប្រាក់ (Record Payment)';
    }
    if (dom.paySubmitBtn) {
        dom.paySubmitBtn.textContent = isEdit ? 'រក្សាទុក (Save Changes)' : 'កត់ត្រាការបង់ប្រាក់ (Record Payment)';
    }
}

/**
 * Opens the same modal to edit an already-recorded payment — its proof
 * image and remark. One payment_batch can cover several subjects paid
 * together, so the context line lists every subject it covers.
 */
export async function openEditPayModal(dom, ApiService, registrationId, paymentBatchId) {
    const row = getRenderedRow(registrationId);
    if (!row || !paymentBatchId) return;

    const { error, data } = await ApiService.request(`${CONFIG.PAYMENT_BATCHES_API}/${paymentBatchId}`);
    if (error) {
        Toast.fire({ icon: 'error', title: data?.message || 'មិនអាចទាញយកការបង់ប្រាក់បានទេ' });
        return;
    }
    const batch = data?.data ?? data;

    state.payingIds = [];
    state.payingStudentId = batch.student?.id ?? row.student?.id ?? null;
    state.editingPaymentBatchId = paymentBatchId;
    setPayModalMode(dom, true);
    clearPayFile(dom);

    const studentName = escapeHtml(studentDisplayName(row.student) || '—');
    const paidAt = batch.paid_at ? escapeHtml(batch.paid_at) : '—';
    if (dom.payContext) {
        dom.payContext.innerHTML = `<strong>${studentName}</strong><br><span class="text-xs text-neutral-500 dark:text-neutral-400">បង់នៅ (Paid at) ${paidAt} — changes apply to every subject on this payment</span>`;
    }
    if (dom.payRemark) dom.payRemark.value = batch.remark ?? '';

    // Show the image already on file; staging a new one replaces it.
    if (batch.invoice_url && dom.payPreview) {
        dom.payPreview.src = batch.invoice_url;
        dom.payPreview.classList.remove('hidden');
        if (dom.payFileName) dom.payFileName.textContent = 'រូបភាពបច្ចុប្បន្ន (Current image) — browse or paste to replace';
    }

    window.RetakePayModal.toggle(true);
}

async function submitEditPayForm(dom, ApiService, onDone) {
    const body = new FormData();
    // POST + _method=PUT: PHP doesn't parse multipart bodies on a real PUT.
    body.append('_method', 'PUT');
    body.append('student_id', state.payingStudentId);
    if (state.payingFile) body.append('invoice_file', state.payingFile);
    body.append('remark', dom.payRemark?.value ?? '');

    const { error, data } = await ApiService.request(`${CONFIG.PAYMENT_BATCHES_API}/${state.editingPaymentBatchId}`, {
        method: 'POST',
        body,
    });

    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || 'មិនអាចរក្សាទុកបានទេ' });
        return;
    }

    Toast.fire({ icon: 'success', title: 'កែប្រែការបង់ប្រាក់ជោគជ័យ! (Payment updated)' });
    window.RetakePayModal.toggle(false);
    state.editingPaymentBatchId = null;
    state.payingStudentId = null;
    clearPayFile(dom);
    onDone();
}

/**
 * Undoes a payment marked by mistake (see
 * RetakeRegistrationController::markUnpaid for what's blocked and why).
 */
export async function handleMarkUnpaid(ApiService, id, onDone) {
    const row = getRenderedRow(id);
    const who = escapeHtml(studentDisplayName(row?.student) || '—');
    const subject = escapeHtml(row?.subject?.name || row?.subject?.code || '—');

    const confirmation = await Swal.fire({
        title: 'ប្តូរទៅមិនទាន់បង់? (Mark as unpaid?)',
        html: `<strong>${who}</strong> — ${subject}<br><span style="font-size:13px;color:#6b7280">ការបង់ប្រាក់ និងការអញ្ជើញ Telegram នៃមុខវិជ្ជានេះនឹងត្រូវលុបចោល។<br>This subject's payment and Telegram invite will be undone.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        confirmButtonText: 'បាទ/ចាស (Yes, mark unpaid)',
        cancelButtonText: 'បោះបង់ (Cancel)',
    });
    if (!confirmation.isConfirmed) return;

    const { error, data } = await ApiService.request(`${CONFIG.REGISTRATIONS_API}/${id}/mark-unpaid`, { method: 'PATCH' });
    if (error) {
        Toast.fire({ icon: 'error', title: data?.message || 'មិនអាចផ្លាស់ប្តូរបានទេ' });
        return;
    }
    Toast.fire({ icon: 'success', title: 'បានប្តូរទៅមិនទាន់បង់ (Marked unpaid)' });
    onDone();
}
