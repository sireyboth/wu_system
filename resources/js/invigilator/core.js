/**
 * core.js — small, always-touched-together infrastructure pieces for the
 * invigilator page.
 */

export const state = {
    debounceTimer: null,
    searchAbortController: null,
    search: '',
    page: 1,
    // Id of the invigilator open in the form modal; null means "Add".
    editingId: null,
    // Invigilator currently shown in the QR modal.
    qrRow: null,
    // Photo staged in the form: a compressed File to upload, and/or a
    // flag to delete the saved one. Both are applied after the profile saves.
    photoFile: null,
    removePhoto: false,
    permissions: window.INVIGILATOR_PERMISSIONS ?? { edit: false, delete: false },
};

export function buildDom() {
    return {
        tableBody: document.getElementById('invigilator-table-body'),
        loader: document.getElementById('loading-overlay'),
        searchInput: document.getElementById('invigilatorSearchInput'),
        addBtn: document.getElementById('invigilatorAddBtn'),

        // Create / edit modal
        modal: document.getElementById('invigilatorModal'),
        modalTitle: document.getElementById('invigilatorModalTitle'),
        form: document.getElementById('invigilatorForm'),
        historyList: document.getElementById('invigilatorHistoryList'),
        historyEmpty: document.getElementById('invigilatorHistoryEmpty'),
        historyTemplate: document.getElementById('invigilatorHistoryTemplate'),
        addHistoryBtn: document.getElementById('invigilatorAddHistoryBtn'),
        photoPreview: document.getElementById('invigilatorPhotoPreview'),
        photoPlaceholder: document.getElementById('invigilatorPhotoPlaceholder'),
        photoPickBtn: document.getElementById('invigilatorPhotoPickBtn'),
        photoRemoveBtn: document.getElementById('invigilatorPhotoRemoveBtn'),
        photoInput: document.getElementById('invigilatorPhotoInput'),

        // QR modal
        qrModal: document.getElementById('invigilatorQrModal'),
        qrImage: document.getElementById('invigilatorQrImage'),
        qrPhoto: document.getElementById('invigilatorQrPhoto'),
        qrNameKh: document.getElementById('invigilatorQrNameKh'),
        qrNameEn: document.getElementById('invigilatorQrNameEn'),
        qrCode: document.getElementById('invigilatorQrCode'),
        qrLink: document.getElementById('invigilatorQrLink'),
        qrDownloadBtn: document.getElementById('invigilatorQrDownloadBtn'),
        qrPrintBtn: document.getElementById('invigilatorQrPrintBtn'),
    };
}

export function openModal(modalEl) {
    if (!modalEl) return;
    const card = modalEl.querySelector(':scope > div');
    modalEl.classList.remove('invisible', 'opacity-0');
    modalEl.classList.add('flex');
    requestAnimationFrame(() => {
        card?.classList.remove('scale-90', 'opacity-0');
        card?.classList.add('scale-100', 'opacity-100');
    });
}

export function closeModal(modalEl, onClosed) {
    if (!modalEl) return;
    const card = modalEl.querySelector(':scope > div');
    modalEl.classList.add('opacity-0');
    card?.classList.remove('scale-100', 'opacity-100');
    card?.classList.add('scale-90', 'opacity-0');

    setTimeout(() => {
        modalEl.classList.add('invisible');
        modalEl.classList.remove('flex');
        onClosed?.();
    }, 300);
}

export function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

export const Toast = typeof Swal !== 'undefined'
    ? Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    })
    : { fire: (opts) => console.log('[Toast fallback]', opts) };
