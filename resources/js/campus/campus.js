/**
 * campus Management System Module
 * Encapsulated to prevent global scope pollution, using event delegation and decoupled UI templates.
 */
(() => {
    'use strict';

    // 1. CONFIGURATION & CONSTANTS
    const CONFIG = {
        API_BASE: '/api/v1/campuses',
        DEBOUNCE_DELAY: 300,
        LOCALE: 'en-GB'
    };

    // 2. CENTRALIZED DOM SELECTORS
    const DOM = {
        form: document.getElementById('addcampusForm'),
        tableBody: document.getElementById('campus-table-body'),
        searchInput: document.getElementById('campusSearchInput'),
        loader: document.getElementById('loading-overlay'),
        modal: document.getElementById('campusModal'),
        modalCard: document.getElementById('modalCard'),
        modalTitle: document.getElementById('modalTitle'),
        submitBtn: document.getElementById('addcampusForm')?.querySelector('button[type="submit"]')
    };

    // Third-party instance verification
    const Toast = typeof Swal !== 'undefined' ? Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    }) : { fire: console.log };

    // 3. PRIVATE MODULE STATE
    const state = {
        isEditMode: false,
        editingcampusId: null,
        debounceTimer: null
    };

    // 4. CORE API SERVICES (Isolated HTTP layer)
const ApiService = {
    async request(url, options = {}) {
        this.toggleLoader(true);
        try {
            // Destructure headers to merge them safely, default to POST/GET dynamically
            const { headers, method = 'GET', body, ...restOptions } = options;

            const response = await fetch(url, {
                method: method, // Explicitly enforce the target HTTP verb
                credentials: 'omit',
                headers: {
                    'Accept': 'application/json',
                    ...headers
                },
                body: body, // Explicitly attach data body payload
                ...restOptions
            });

            const contentType = response.headers.get("content-type");
            const isJson = contentType && contentType.includes("application/json");
            const result = isJson ? await response.json() : null;

            if (!response.ok) {
                return { error: true, status: response.status, data: result };
            }
            return { error: false, status: response.status, data: result };
        } catch (err) {
            console.error(`[API Error] Action failed on ${url}:`, err);
            return { error: true, status: 500, data: null };
        } finally {
            this.toggleLoader(false);
        }
    },

    toggleLoader(show) {
        if (DOM.loader) {
            DOM.loader.classList.toggle('hidden', !show);
        }
    }
};

    // 5. CORE WORKFLOW CONTROLLERS
    async function loadcampuss(searchQuery = '') {
        const url = `${CONFIG.API_BASE}?search=${encodeURIComponent(searchQuery)}`;
        const { error, data } = await ApiService.request(url);

        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load campuss' });
            return;
        }

        // Handle both object-paginated dynamic data lists or straight array responses safely
        const records = data && Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
        renderTable(records);
    }

    async function handleEditAction(id) {
        const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
        if (error) {
            Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យបានទេ' });
            return;
        }

        const payload = data.data || data;
        state.isEditMode = true;
        state.editingcampusId = id;

        if (DOM.modalTitle) DOM.modalTitle.textContent = 'កែប្រែព័ត៌មានវេន';
        if (DOM.submitBtn) DOM.submitBtn.textContent = 'ធ្វើបច្ចុប្បន្នភាព';

        if (DOM.form) {
            ['name_kh', 'name_en', 'shortcut', 'remark'].forEach(field => {
                const element = DOM.form.querySelector(`[name="${field}"]`);
                if (element) element.value = payload[field] ?? '';
            });
        }
        toggleModal(true);
    }

    async function handleDeleteAction(id) {
        const confirmation = await Swal.fire({
            title: 'តើអ្នកប្រាកដជាចង់លុបមែនទេ?',
            text: "ទិន្នន័យនេះមិនអាចយកមកវិញបានឡើយ!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'បាទ/ចាស លុបវា!',
            cancelButtonText: 'បោះបង់'
        });

        if (!confirmation.isConfirmed) return;

        const { error } = await ApiService.request(`${CONFIG.API_BASE}/${id}`, { method: 'DELETE' });
        if (!error) {
            Toast.fire({ icon: 'success', title: 'លុបទិន្នន័យបានជោគជ័យ!' });
            loadcampuss(DOM.searchInput?.value || '');
        } else {
            Toast.fire({ icon: 'error', title: 'មានបញ្ហាមិនអាចលុបទិន្នន័យនេះបាន' });
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        if (!DOM.form || !DOM.submitBtn) return;

        DOM.submitBtn.disabled = true;
        const formData = new FormData(DOM.form);
        const payload = {};

        for (const [key, value] of formData.entries()) {
            if (key === 'search') continue;
            const cleanVal = value.toString().trim();
            payload[key] = cleanVal === '' ? null : cleanVal;
        }

        const url = state.isEditMode ? `${CONFIG.API_BASE}/${state.editingcampusId}` : CONFIG.API_BASE;
        const method = state.isEditMode ? 'PUT' : 'POST';

        const { error, status, data } = await ApiService.request(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!error) {
            Toast.fire({
                icon: 'success',
                title: state.isEditMode ? 'ធ្វើបច្ចុប្បន្នភាពជោគជ័យ!' : 'បង្កើតវេនជោគជ័យ!',
                text: state.isEditMode ? 'ព័ត៌មានត្រូវបានកែប្រែរួចរាល់។' : `${payload.name_kh} has been added.`
            });
            resetFormState();
            toggleModal(false);
            loadcampuss();
        } else if (status === 422 && data) {
            const errorMessages = data.errors ? Object.values(data.errors).flat() : ['Validation failed'];
            Toast.fire({
                icon: 'warning',
                title: 'ពិនិត្យទិន្នន័យឡើងវិញ',
                html: `<div class="text-left text-xs text-rose-500 mt-1 list-disc pl-4">${errorMessages.map(msg => `<li>${msg}</li>`).join('')}</div>`
            });
        } else {
            Toast.fire({
                icon: 'error',
                title: 'មានបញ្ហាភ្ជាប់ទៅកាន់ប្រព័ន្ធ',
                text: data?.message || 'Internal Server Error (500).'
            });
        }
        DOM.submitBtn.disabled = false;
    }

    // UI RENDERING & COMPONENT TEMPLATES
function renderTable(campuss) {
    if (!DOM.tableBody) return;

    if (!campuss || campuss.length === 0) {
       DOM.tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-neutral-500">No records found.</td></tr>';
        return;
    }

    // Restore responsive layout classes dynamically
    DOM.tableBody.className = "grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 md:p-0 md:table-row-group md:divide-y md:divide-neutral-200 md:dark:divide-white/5";

    DOM.tableBody.innerHTML = campuss.map((campus, index) => {
        const formattedDate = new Date(campus.created_at).toLocaleDateString(CONFIG.LOCALE, {
            day: '2-digit', month: 'short', year: 'numeric'
        });

        // Fixed a subtle data bug from your snippet where campus.name_kh was printed twice instead of English variant below it
        const nameKhmer = campus.name_kh ?? '<span class="text-neutral-400 italic">N/A</span>';
        const nameEnglish = campus.name_en ?? '<span class="text-neutral-400 italic">N/A</span>';

        return `
            <tr class="block relative p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-sm hover:shadow-md md:shadow-none md:border-0 md:border-b md:rounded-none md:p-0 md:bg-transparent md:dark:bg-transparent campus hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all duration-200 md:table-row">

                <td class="px-6 py-4 text-neutral-500 font-mono text-sm">${index + 1}</td>

                <!-- Khmer Name Element -->
                <td class="block md:table-cell px-0 py-1.5 md:px-6 md:py-4 font-bold text-indigo-600 dark:text-indigo-400">
                    <span class="text-xs text-neutral-400 font-normal md:hidden block mb-0.5">Name Khmer</span>
                    ${nameKhmer}
                </td>

                <!-- Primary Identity Section (Combined Visual Component) -->
                <td class="block md:table-cell px-0 py-2 md:px-6 md:py-4">
                    <span class="text-xs text-neutral-400 font-normal md:hidden block mb-1.5">English Identity</span>
                    <div class="flex items-center">
                        <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-neutral-900 dark:text-white">${nameEnglish}</div>
                            <div class="text-xs text-neutral-400 font-mono">${campus.shortcut ?? 'No Code'}</div>
                        </div>
                    </div>
                </td>

                <!-- Shortcut Column Descriptor -->
                <td class="block md:table-cell px-0 py-1.5 md:px-6 md:py-4">
                    <span class="text-xs text-neutral-400 font-normal md:hidden block">Shortcut</span>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 max-w-[250px] truncate" title="${campus.shortcut ?? ''}">
                        ${campus.shortcut ?? '<span class="italic opacity-40 text-xs">No Shortcut</span>'}
                    </p>
                </td>

                <!-- Remarks Text Region -->
                <td class="block md:table-cell px-0 py-1.5 md:px-6 md:py-4">
                    <span class="text-xs text-neutral-400 font-normal md:hidden block">Remark</span>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 max-w-[250px] truncate" title="${campus.remark ?? ''}">
                        ${campus.remark ?? '<span class="italic opacity-40 text-xs">No remarks</span>'}
                    </p>
                </td>

                <!-- Created Timestamp Segment -->
                <td class="block md:table-cell px-0 py-1.5 md:px-6 md:py-4 text-xs text-neutral-500 font-mono">
                    <span class="text-xs text-neutral-400 font-normal md:hidden block font-sans mb-0.5">Created At</span>
                    ${formattedDate}
                </td>

                <!-- Action Button Controls (Anchored cleanly across both modes) -->
                <td class="block md:table-cell px-0 pt-4 pb-1 md:p-6 text-right border-t border-neutral-100 dark:border-white/5 mt-3 md:mt-0 md:border-0">
                    <div class="flex justify-end gap-2">
                        <button data-action="edit" data-id="${campus.id}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-colors" title="Edit campus">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button data-action="delete" data-id="${campus.id}" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors" title="Delete campus">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>`;
    }).join('');
}

    // 7. INTERACTIVE & MODAL TRANSLATION ENGINE
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

            setTimeout(() => {
                DOM.form?.querySelector('[name="name_kh"]')?.focus();
            }, 250);
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
        if (DOM.form) DOM.form.reset();
        state.isEditMode = false;
        state.editingcampusId = null;

        if (DOM.modalTitle) DOM.modalTitle.textContent = 'បន្ថែមសាស្ត្រាចារ្យថ្មី';
        if (DOM.submitBtn) DOM.submitBtn.textContent = 'រក្សាទុក';

        document.querySelectorAll('.smart-hint').forEach(hint => {
            hint.classList.add('opacity-0', 'scale-95', 'translate-y-1');
        });
    }

    // 8. EVENT ATTACHMENTS PIPELINE
    function initEvents() {
        // Expose toggleModal securely only for explicit HTML elements like header close/open triggers
        window.AppModal = { toggle: (open) => toggleModal(open) };

        // Search Input Engine with Clean Debouncing
        DOM.searchInput?.addEventListener('input', (e) => {
            clearTimeout(state.debounceTimer);
            state.debounceTimer = setTimeout(() => {
                loadcampuss(e.target.value);
            }, CONFIG.DEBOUNCE_DELAY);
        });

        // Form Submit
        DOM.form?.addEventListener('submit', handleFormSubmit);

        // Modern Event Delegation: Intercept Action Buttons without explicit tag onClick attributes
        DOM.tableBody?.addEventListener('click', (e) => {
            const targetBtn = e.target.closest('button[data-action]');
            if (!targetBtn) return;

            const action = targetBtn.getAttribute('data-action');
            const targetId = targetBtn.getAttribute('data-id');

            if (action === 'edit') handleEditAction(targetId);
            if (action === 'delete') handleDeleteAction(targetId);
        });

        // Interactive Tooltips Hint Dynamic Engine
        const inputsWithHints = document.querySelectorAll('#addcampusForm input, #addcampusForm textarea');
        inputsWithHints.forEach(input => {
            const hintBox = input.parentElement.querySelector('.smart-hint');
            const textMessage = input.getAttribute('data-hint');
            if (!hintBox || !textMessage) return;

            hintBox.textContent = textMessage;

            input.addEventListener('input', function() {
                const hasValue = this.value.trim().length > 0;
                hintBox.classList.toggle('opacity-0', !hasValue);
                hintBox.classList.toggle('scale-95', !hasValue);
                hintBox.classList.toggle('translate-y-1', !hasValue);
                hintBox.classList.toggle('opacity-100', hasValue);
                hintBox.classList.toggle('scale-100', hasValue);
                hintBox.classList.toggle('-translate-y-2', hasValue);
            });

            input.addEventListener('blur', () => {
                hintBox.classList.remove('opacity-100', 'scale-100', '-translate-y-2');
                hintBox.classList.add('opacity-0', 'scale-95', 'translate-y-1');
            });
        });
    }

    // 9. INITIALIZE ENGINE RUNNING
    document.addEventListener('DOMContentLoaded', () => {
        initEvents();
        loadcampuss();
    });
})();
