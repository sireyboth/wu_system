export const state = {
    debounceTimer: null,
    searchAbortController: null,
    search: '',
    page: 1,
    // Id of the transcript open in the form modal; null means "New".
    editingId: null,
    activeYear: 1,
    subjects: window.TRANSCRIPT_DATA?.subjects ?? [],
    permissions: window.TRANSCRIPT_PERMISSIONS ?? { edit: false, delete: false },
};
