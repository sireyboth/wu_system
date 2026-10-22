/**
 * Term Management module — academic year/semester records. Any number can
 * be active at once (different batches genuinely run on different
 * calendars at the same time — e.g. one batch's semester starts in
 * September, another's in October). "Active" just marks a term as
 * currently in use; when something needs a default term with no other
 * context (Excel import, an advance-semester form nobody filled the term
 * picker on), the backend picks whichever active term's date range covers
 * today — see Term::resolveDefault().
 */
(() => {
    'use strict';

    const CONFIG = {
        API_BASE: '/api/v1/terms',
        DEBOUNCE_DELAY: 300,
        LOCALE: 'en-GB',
    };

    const DOM = {
        form: document.getElementById('termForm'),
        tableBody: document.getElementById('term-table-body'),
        searchInput: document.getElementById('termSearchInput'),
        loader: document.getElementById('loading-overlay'),
        modal: document.getElementById('termModal'),
        modalCard: document.getElementById('modalCard'),
        modalTitle: document.getElementById('modalTitle'),
        submitBtn: document.getElementById('termForm')?.querySelector('button[type="submit"]'),
    };

    const Toast = typeof Swal !== 'undefined' ? Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    }) : { fire: console.log };

    const state = {
        isEditMode: false,
        editingTermId: null,
        debounceTimer: null,
    };

    const ApiService = {
        async request(url, options = {}) {
            this.toggleLoader(true);
            try {
                const { headers, method = 'GET', body, ...rest } = options;
                const response = await fetch(url, {
                    method,
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json', ...headers },
                    body,
                    ...rest,
                });
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                const result = isJson ? await response.json() : null;
                if (!response.ok) return { error: true, status: response.status, data: result };
                return { error: false, status: response.status, data: result };
            } catch (err) {
                console.error(`[API Error] Action failed on ${url}:`, err);
                return { error: true, status: 500, data: null };
            } finally {
                this.toggleLoader(false);
            }
        },
        toggleLoader(show) {
            DOM.loader?.classList.toggle('hidden', !show);
        },
    };

    async function loadTerms(searchQuery = '') {
        const url = `${CONFIG.API_BASE}?search=${encodeURIComponent(searchQuery)}`;
        const { error, data } = await ApiService.request(url);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load terms' });
            return;
        }
        const records = data && Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
        renderTable(records);
    }

    async function handleEditAction(id) {
        const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Could not load this term' });
            return;
        }
        const payload = data.data || data;
        state.isEditMode = true;
        state.editingTermId = id;

        if (DOM.modalTitle) DOM.modalTitle.textContent = 'Edit Term';
        if (DOM.submitBtn) DOM.submitBtn.textContent = 'Update';

        if (DOM.form) {
            ['year', 'semester', 'code', 'name', 'start_date', 'end_date', 'remark'].forEach((field) => {
                const el = DOM.form.querySelector(`[name="${field}"]`);
                if (el) el.value = payload[field] ?? '';
            });
            const activeCheckbox = DOM.form.querySelector('[name="is_active"]');
            if (activeCheckbox) activeCheckbox.checked = !!payload.is_active;
        }
        toggleModal(true);
    }

    async function handleActivateAction(id) {
        const { error } = await ApiService.request(`${CONFIG.API_BASE}/${id}/activate`, { method: 'PATCH' });
        if (!error) {
            Toast.fire({ icon: 'success', title: 'Term marked active' });
            loadTerms(DOM.searchInput?.value || '');
        } else {
            Toast.fire({ icon: 'error', title: 'Could not activate this term' });
        }
    }

    async function handleDeactivateAction(id) {
        const { error } = await ApiService.request(`${CONFIG.API_BASE}/${id}/deactivate`, { method: 'PATCH' });
        if (!error) {
            Toast.fire({ icon: 'success', title: 'Term deactivated' });
            loadTerms(DOM.searchInput?.value || '');
        } else {
            Toast.fire({ icon: 'error', title: 'Could not deactivate this term' });
        }
    }

    async function handleDeleteAction(id) {
        const confirmation = await Swal.fire({
            title: 'Delete this term?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it',
        });
        if (!confirmation.isConfirmed) return;

        const { error } = await ApiService.request(`${CONFIG.API_BASE}/${id}`, { method: 'DELETE' });
        if (!error) {
            Toast.fire({ icon: 'success', title: 'Deleted successfully' });
            loadTerms(DOM.searchInput?.value || '');
        } else {
            Toast.fire({ icon: 'error', title: 'Could not delete this term' });
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        if (!DOM.form || !DOM.submitBtn) return;

        DOM.submitBtn.disabled = true;
        const formData = new FormData(DOM.form);
        const payload = {};
        for (const [key, value] of formData.entries()) {
            payload[key] = value.toString().trim() || null;
        }
        payload.is_active = DOM.form.querySelector('[name="is_active"]')?.checked ?? false;

        const url = state.isEditMode ? `${CONFIG.API_BASE}/${state.editingTermId}` : CONFIG.API_BASE;
        const method = state.isEditMode ? 'PUT' : 'POST';

        const { error, status, data } = await ApiService.request(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!error) {
            Toast.fire({ icon: 'success', title: state.isEditMode ? 'Term updated' : 'Term created' });
            toggleModal(false);
            loadTerms();
        } else if (status === 422 && data) {
            const errorMessages = data.errors ? Object.values(data.errors).flat() : ['Validation failed'];
            Toast.fire({
                icon: 'warning',
                title: 'Please check the form',
                html: `<div class="text-left text-xs text-rose-500 mt-1 list-disc pl-4">${errorMessages.map((m) => `<li>${m}</li>`).join('')}</div>`,
            });
        } else {
            Toast.fire({ icon: 'error', title: data?.message || 'Internal Server Error (500).' });
        }
        DOM.submitBtn.disabled = false;
    }

    function renderTable(terms) {
        if (!DOM.tableBody) return;
        if (!terms || terms.length === 0) {
            DOM.tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-neutral-500">No terms found.</td></tr>';
            return;
        }

        DOM.tableBody.innerHTML = terms.map((term, index) => {
            const start = term.start_date ? new Date(term.start_date).toLocaleDateString(CONFIG.LOCALE, { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
            const end = term.end_date ? new Date(term.end_date).toLocaleDateString(CONFIG.LOCALE, { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
            const activeBadge = term.is_active
                ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Active</span>'
                : '<span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-neutral-100 text-neutral-500 dark:bg-white/5 dark:text-neutral-400">Inactive</span>';

            return `
                <tr class="block relative p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-sm hover:shadow-md md:shadow-none md:border-0 md:border-b md:rounded-none md:p-0 md:bg-transparent md:dark:bg-transparent hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all duration-200 md:table-row">
                    <td class="block md:table-cell px-0 md:px-6 py-1.5 md:py-4 text-neutral-500 font-mono text-sm">${index + 1}</td>
                    <td class="block md:table-cell px-0 md:px-6 py-1.5 md:py-4 font-bold text-indigo-600 dark:text-indigo-400 font-mono">${term.code ?? '—'}</td>
                    <td class="block md:table-cell px-0 md:px-6 py-1.5 md:py-4">${term.name ?? '—'} (${term.year ?? '—'})</td>
                    <td class="block md:table-cell px-0 md:px-6 py-1.5 md:py-4">Semester ${term.semester ?? '—'}</td>
                    <td class="block md:table-cell px-0 md:px-6 py-1.5 md:py-4 text-xs">${start} → ${end}</td>
                    <td class="block md:table-cell px-0 md:px-6 py-1.5 md:py-4">${activeBadge}</td>
                    <td class="block md:table-cell px-0 pt-4 pb-1 md:p-6 text-right border-t border-neutral-100 dark:border-white/5 mt-3 md:mt-0 md:border-0">
                        <div class="flex justify-end gap-2">
                            ${term.is_active ? `
                            <button data-action="deactivate" data-id="${term.id}" class="p-2 text-neutral-500 hover:bg-neutral-100 dark:hover:bg-white/10 rounded-lg transition-colors" title="Deactivate">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>` : `
                            <button data-action="activate" data-id="${term.id}" class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-colors" title="Make active">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>`}
                            <button data-action="edit" data-id="${term.id}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-colors" title="Edit term">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button data-action="delete" data-id="${term.id}" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors" title="Delete term">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>`;
        }).join('');
    }

    function toggleModal(forceOpen = null) {
        if (!DOM.modal || !DOM.modalCard) return;
        const isOpen = DOM.modal.classList.contains('flex');
        const makeOpen = forceOpen !== null ? forceOpen : !isOpen;

        if (makeOpen) {
            DOM.modal.classList.remove('invisible');
            DOM.modal.classList.add('flex');
            requestAnimationFrame(() => {
                DOM.modal.classList.remove('opacity-0');
                DOM.modalCard.classList.remove('scale-90', 'opacity-0');
                DOM.modalCard.classList.add('scale-100', 'opacity-100');
            });
        } else {
            DOM.modal.classList.add('opacity-0');
            DOM.modalCard.classList.remove('scale-100', 'opacity-100');
            DOM.modalCard.classList.add('scale-90', 'opacity-0');
            setTimeout(() => {
                DOM.modal.classList.add('invisible');
                DOM.modal.classList.remove('flex');
                resetFormState();
            }, 300);
        }
    }

    function resetFormState() {
        DOM.form?.reset();
        state.isEditMode = false;
        state.editingTermId = null;
        if (DOM.modalTitle) DOM.modalTitle.textContent = 'Create New Term';
        if (DOM.submitBtn) DOM.submitBtn.textContent = 'Save';
    }

    function initEvents() {
        window.AppModal = { toggle: (open) => toggleModal(open) };

        DOM.searchInput?.addEventListener('input', (e) => {
            clearTimeout(state.debounceTimer);
            state.debounceTimer = setTimeout(() => loadTerms(e.target.value), CONFIG.DEBOUNCE_DELAY);
        });

        DOM.form?.addEventListener('submit', handleFormSubmit);

        DOM.tableBody?.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            const action = btn.getAttribute('data-action');
            const id = btn.getAttribute('data-id');
            if (action === 'edit') handleEditAction(id);
            if (action === 'delete') handleDeleteAction(id);
            if (action === 'activate') handleActivateAction(id);
            if (action === 'deactivate') handleDeactivateAction(id);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initEvents();
        loadTerms();
    });
})();
