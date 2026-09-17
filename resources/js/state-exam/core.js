/**
 * core.js — small, always-touched-together infrastructure pieces.
 * These have no logic of their own; they exist to be imported by
 * files that DO have logic (api-service, student-form, index, etc.)
 */

/**
 * Single shared mutable state object for the Student module.
 * Deliberately NOT frozen/exported as individual consts — every consumer
 * needs to read/write the *same* object reference.
 */
export const state = {
    isEditMode: false,
    editingId: null,
    debounceTimer: null,
    searchAbortController: null, // used by api-service to cancel stale searches
    showingTrash: false,
    sortKey: 'room',
    sortDir: 'asc', // 'asc' | 'desc'

    examTerms: [],      // every exam term (any category, active or not) — admin sees all
    examCategories: [], // manageable list: State Exam, Scholarship, etc.
    termFilterId: '',   // '' = all terms, on the exam-rooms table
    editingTermId: null, // set while the Exam Term modal is editing an existing term (separate from isEditMode/editingId, which are the room modal's)
};

/**
 * Builds a fresh DOM selector map. Call once, on DOMContentLoaded,
 * from index.js and pass the result into everything else.
 *
 * Kept as a factory (not a module-level constant) so other page modules
 * (lecturers, guardians, etc.) can each build their own shape without
 * fighting over a shared `window.DOM`.
 */
export function buildDom() {
    return {
        form: document.getElementById('stateExamForm'),
        tableBody: document.getElementById('state-exam-table-body'),
        searchInput: document.getElementById('state-examSearchInput'),
        loader: document.getElementById('loading-overlay'),
        modal: document.getElementById('stateExamModal'),
        modalCard: document.getElementById('modalCard'),
        modalTitle: document.getElementById('modalTitle'),
        // Submit button lives outside <form id="stateExamForm"> now (in the fixed
        // footer) and is wired to it via the HTML `form` attribute, so the form
        // body can scroll independently while the footer stays pinned.
        submitBtn: document.querySelector('button[type="submit"][form="stateExamForm"]'),

        // Term filter on the exam-rooms table
        termFilterSelect: document.getElementById('examTermFilterSelect'),

        // Exam Term select inside the room create/edit modal
        roomExamTermSelect: document.getElementById('roomExamTermSelect'),

        // "+ New Exam Term" / edit modal
        newExamTermBtn: document.getElementById('newExamTermBtn'),
        editExamTermBtn: document.getElementById('editExamTermBtn'),
        examTermModal: document.getElementById('examTermModal'),
        examTermModalCard: document.getElementById('examTermModalCard'),
        examTermModalTitle: document.getElementById('examTermModalTitle'),
        examTermForm: document.getElementById('examTermForm'),
        examTermCategoryInput: document.getElementById('examTermCategoryInput'),
        examTermCategoryList: document.getElementById('examTermCategoryList'),
        examTermCampusSelect: document.getElementById('examTermCampusSelect'),
        examTermTitleInput: document.getElementById('examTermTitleInput'),
        examTermDateInput: document.getElementById('examTermDateInput'),
        examTermActiveInput: document.getElementById('examTermActiveInput'),
        examTermSlotsContainer: document.getElementById('examTermSlotsContainer'),
        addSlotBtn: document.getElementById('addSlotBtn'),
        examTermSubmitBtn: document.getElementById('examTermSubmitBtn'),

        // Export / Import
        exportBtn: document.getElementById('examStatesExportBtn'),
        importBtn: document.getElementById('examStatesImportBtn'),
        importFileInput: document.getElementById('examStatesImportFileInput'),
    };
}

/**
 * Thin wrapper around SweetAlert2's toast mixin.
 * Falls back to console.log so the app doesn't crash on pages
 * that forget to load SweetAlert2.
 */
export const Toast = typeof Swal !== 'undefined'
    ? Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    })
    : { fire: (opts) => console.log('[Toast fallback]', opts) };
