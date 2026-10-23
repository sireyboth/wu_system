export const state = {
    debounceTimer: null,
    searchAbortController: null,
    search: '',
    page: 1,
    // Id of the transcript open in the form modal; null means "New".
    editingId: null,
    activeYear: 1,
    // Full subject record open in the "Edit Subject" modal.
    editingSubject: null,
    subjects: window.TRANSCRIPT_DATA?.subjects ?? [],
    // Print layouts { transcript: 'Transcript', provisional: 'Provisional Certificate', … }.
    docTypes: window.TRANSCRIPT_DATA?.docTypes ?? {},
    permissions: window.TRANSCRIPT_PERMISSIONS ?? { edit: false, delete: false, subject_edit: false },
};
