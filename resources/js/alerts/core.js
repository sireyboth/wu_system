/**
 * core.js — shared state, DOM refs, and the SweetAlert2 toast mixin for the
 * Alerts module. Mirrors the same small pattern used by resources/js/stateExam.
 */

export const API_BASE = '/api/v1/alerts';

export const state = {
    isEditMode: false,
    editingId: null,
    debounceTimer: null,
};

export function buildDom() {
    return {
        form: document.getElementById('alertForm'),
        loader: document.getElementById('loading-overlay'),
        modal: document.getElementById('alertModal'),
        modalCard: document.getElementById('modalCard'),
        modalTitle: document.getElementById('modalTitle'),
        deleteBtn: document.getElementById('deleteAlertBtn'),
        searchInput: document.getElementById('alertSearchInput'),
        boardView: document.getElementById('boardView'),
        searchView: document.getElementById('searchView'),
        highSection: document.getElementById('highSection'),
        mediumSection: document.getElementById('mediumSection'),
        laterSection: document.getElementById('laterSection'),
        doneTableBody: document.getElementById('alert-done-table-body'),
        searchSection: document.getElementById('searchSection'),
        remindEnabled: document.getElementById('remindEnabled'),
        remindIntervalWrap: document.getElementById('remindIntervalWrap'),
        remindIntervalValue: document.getElementById('remindIntervalValue'),
        remindIntervalUnit: document.getElementById('remindIntervalUnit'),
        remindIntervalMinutes: document.getElementById('remindIntervalMinutes'),
        repeatType: document.getElementById('repeatType'),
        repeatIntervalWrap: document.getElementById('repeatIntervalWrap'),
        repeatUntilWrap: document.getElementById('repeatUntilWrap'),
    };
}

export const Toast = typeof Swal !== 'undefined'
    ? Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true })
    : { fire: (opts) => console.log('[Toast fallback]', opts) };

export function toggleLoader(dom, show) {
    dom.loader?.classList.toggle('hidden', !show);
    dom.loader?.classList.toggle('flex', show);
}

export function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}
