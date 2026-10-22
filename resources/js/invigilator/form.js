import { CONFIG } from './config.js';
import { state, Toast } from './core.js';
import { getRenderedRow } from './table-render.js';
import { compressImage } from '../retake-payment/image-compress.js';

const PROFILE_FIELDS = ['name_kh', 'name_en', 'code', 'batch', 'department', 'room', 'valid_until', 'remark'];

/** Opens the form empty (Add) or pre-filled from a rendered row (Edit). */
export function openForm(dom, id = null) {
    const row = id ? getRenderedRow(id) : null;
    state.editingId = row ? row.id : null;

    dom.form?.reset();
    PROFILE_FIELDS.forEach((name) => {
        const input = dom.form?.elements[name];
        if (input) input.value = row?.[name] ?? '';
    });

    state.photoFile = null;
    state.removePhoto = false;
    if (dom.photoInput) dom.photoInput.value = '';
    showPhoto(dom, row?.photo_url ?? null);

    dom.historyList.innerHTML = '';
    (row?.histories ?? []).forEach((h) => addHistoryRow(dom, h));
    syncHistoryEmpty(dom);

    if (dom.modalTitle) {
        dom.modalTitle.textContent = row
            ? 'កែសម្រួលអ្នកឃ្លាំមើល (Edit Invigilator)'
            : 'បន្ថែមអ្នកឃ្លាំមើល (Add Invigilator)';
    }

    window.InvigilatorModal.toggle(true);
}

function showPhoto(dom, url) {
    const has = Boolean(url);
    if (dom.photoPreview) {
        if (has) dom.photoPreview.src = url;
        else dom.photoPreview.removeAttribute('src');
        dom.photoPreview.classList.toggle('hidden', !has);
    }
    dom.photoPlaceholder?.classList.toggle('hidden', has);
    dom.photoRemoveBtn?.classList.toggle('hidden', !has);
}

/** Choose / remove photo. Nothing is sent until the form is saved. */
export function bindPhotoPicker(dom) {
    dom.photoPickBtn?.addEventListener('click', () => dom.photoInput?.click());

    dom.photoInput?.addEventListener('change', async () => {
        const file = dom.photoInput.files?.[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) {
            Toast.fire({ icon: 'warning', title: 'សូមជ្រើសរើសរូបភាព (Please choose an image)' });
            return;
        }

        // Same downscale/re-encode as payment proofs — a phone photo can be
        // several MB, far more than a card photo needs.
        // WebP, not the helper's default JPEG — JPEG has no transparency,
        // and a background-removed cutout needs it to pop out of the card's arch.
        state.photoFile = await compressImage(file, { maxDimension: 1000, type: 'image/webp', quality: 0.85 });
        state.removePhoto = false;
        showPhoto(dom, URL.createObjectURL(state.photoFile));
    });

    dom.photoRemoveBtn?.addEventListener('click', () => {
        state.photoFile = null;
        state.removePhoto = true;
        if (dom.photoInput) dom.photoInput.value = '';
        showPhoto(dom, null);
    });
}

/** Uploads/deletes the staged photo once the profile has an id. */
async function applyPhoto(ApiService, id) {
    if (state.photoFile) {
        const body = new FormData();
        body.append('photo', state.photoFile, state.photoFile.name || 'photo.jpg');
        return ApiService.request(`${CONFIG.INVIGILATORS_API}/${id}/photo`, { method: 'POST', body });
    }
    if (state.removePhoto) {
        return ApiService.request(`${CONFIG.INVIGILATORS_API}/${id}/photo`, { method: 'DELETE' });
    }
    return { error: false };
}

/** Clones the <template> into a new history row, optionally pre-filled. */
export function addHistoryRow(dom, history = {}) {
    const fragment = dom.historyTemplate.content.cloneNode(true);
    const rowEl = fragment.querySelector('.history-row');

    ['id', 'description', 'date', 'remark'].forEach((field) => {
        const input = rowEl.querySelector(`[data-field="${field}"]`);
        if (input) input.value = history[field] ?? '';
    });
    setRating(rowEl, history.rating ?? 0);

    dom.historyList.appendChild(fragment);
    syncHistoryEmpty(dom);
    return rowEl;
}

/** Wires remove + star clicks for every row, present and future. */
export function bindHistoryList(dom) {
    dom.historyList?.addEventListener('click', (e) => {
        const remove = e.target.closest('[data-action="remove-history"]');
        if (remove) {
            remove.closest('.history-row')?.remove();
            syncHistoryEmpty(dom);
            return;
        }

        const star = e.target.closest('[data-star]');
        if (star) setRating(star.closest('.history-row'), Number(star.dataset.star));
    });
}

function setRating(rowEl, rating) {
    const input = rowEl?.querySelector('[data-field="rating"]');
    if (!input) return;

    input.value = rating > 0 ? rating : '';
    rowEl.querySelectorAll('[data-star]').forEach((btn) => {
        const value = Number(btn.dataset.star);
        if (value === 0) return;
        const on = value <= rating;
        btn.classList.toggle('text-amber-400', on);
        btn.classList.toggle('text-neutral-300', !on);
        btn.classList.toggle('dark:text-neutral-600', !on);
    });
}

function syncHistoryEmpty(dom) {
    const hasRows = dom.historyList?.querySelector('.history-row');
    dom.historyEmpty?.classList.toggle('hidden', Boolean(hasRows));
}

function collectPayload(dom) {
    const payload = {};
    PROFILE_FIELDS.forEach((name) => {
        const value = dom.form.elements[name]?.value.trim() ?? '';
        payload[name] = value === '' ? null : value;
    });

    payload.histories = [...dom.historyList.querySelectorAll('.history-row')].map((rowEl) => {
        const get = (field) => rowEl.querySelector(`[data-field="${field}"]`)?.value.trim() || null;
        return {
            id: get('id') ? Number(get('id')) : null,
            description: get('description'),
            date: get('date'),
            rating: get('rating') ? Number(get('rating')) : null,
            remark: get('remark'),
        };
    });

    return payload;
}

export async function submitForm(dom, ApiService, onDone) {
    const editing = state.editingId;
    const url = editing ? `${CONFIG.INVIGILATORS_API}/${editing}` : CONFIG.INVIGILATORS_API;

    const { error, data } = await ApiService.request(url, {
        method: editing ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(collectPayload(dom)),
    });

    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || 'មិនអាចរក្សាទុកបានទេ' });
        return;
    }

    // The profile is saved at this point; a photo failure shouldn't hide
    // that, so it's reported separately and the form still closes.
    const savedId = data?.data?.id ?? editing;
    const photo = savedId ? await applyPhoto(ApiService, savedId) : { error: false };

    if (photo.error) {
        const photoError = photo.data?.errors ? Object.values(photo.data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'warning', title: `រក្សាទុកហើយ តែរូបថតមិនបាន (Saved, but photo failed): ${photoError || photo.data?.message || ''}` });
    } else {
        Toast.fire({ icon: 'success', title: 'រក្សាទុកជោគជ័យ!' });
    }

    window.InvigilatorModal.toggle(false);
    state.editingId = null;
    state.photoFile = null;
    state.removePhoto = false;
    onDone();
}

export async function handleDelete(ApiService, id, onDone) {
    const row = getRenderedRow(id);
    const confirmation = await Swal.fire({
        title: 'តើអ្នកប្រាកដជាចង់លុបមែនទេ?',
        text: `${row?.name_en ?? ''} — ទិន្នន័យនេះនឹងផ្លាស់ទៅធុងសំរាម (Moved to trash). Their QR card will stop working.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'បាទ/ចាស លុបវា!',
        cancelButtonText: 'បោះបង់',
    });
    if (!confirmation.isConfirmed) return;

    const { error, data } = await ApiService.request(`${CONFIG.INVIGILATORS_API}/${id}`, { method: 'DELETE' });
    if (error) {
        Toast.fire({ icon: 'error', title: data?.message || 'មិនអាចលុបបានទេ' });
        return;
    }
    Toast.fire({ icon: 'success', title: 'លុបទិន្នន័យបានជោគជ័យ!' });
    onDone();
}
