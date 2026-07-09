/**
 * student Management System Module
 * Encapsulated to prevent global scope pollution, using event delegation and decoupled UI templates.
 */
(() => {
    'use strict';

    // 1. CONFIGURATION & CONSTANTS (Must be at the absolute top)
const CONFIG = {
    API_BASE: '/api/v1/students',
    DEBOUNCE_DELAY: 300,
    LOCALE: 'en-GB'
};
// Append the new API routes to your existing CONFIG map object
CONFIG.API_LOOKUPS = {
    provinces: '/api/v1/provinces',
    districts: '/api/v1/districts',
    communes: '/api/v1/communes',
    villages: '/api/v1/villages',
    batches: '/api/v1/batches',
    majors: '/api/v1/majors'
};

// 2. CENTRALIZED DOM SELECTORS
const DOM = {
    form: document.getElementById('studentForm'),
    tableBody: document.getElementById('student-table-body'),
    searchInput: document.getElementById('studentSearchInput'),
    loader: document.getElementById('loading-overlay'),
    modal: document.getElementById('studentModal'),
    modalCard: document.getElementById('modalCard'),
    modalTitle: document.getElementById('modalTitle'),
    submitBtn: document.getElementById('studentForm')?.querySelector('button[type="submit"]')
};
// 2. Export it globally right here
window.DOM = DOM;
window.CONFIG = CONFIG;
/**
 * Utility helper to populate a select element with options dynamically
 */
function fillSelectOptions(element, items, valueField = 'id', textField = 'name') {
    if (!element) return;
    const placeholder = element.options[0];
    element.innerHTML = '';
    if (placeholder) element.appendChild(placeholder);

    items.forEach(item => {
        const opt = document.createElement('option');

        if (typeof item === 'string') {
            opt.value = item;
            opt.textContent = item;
        } else {
            // CRITICAL: Always pass the numeric primary key ID for all fields
            const recordId = item[valueField] ?? item['id'];
            opt.value = recordId;
            opt.setAttribute('data-id', recordId);

            // Visual text label shown to the user
            opt.textContent = item['name_kh'] || item['name'] || item[textField];
        }
        element.appendChild(opt);
    });
}

/**
 * Centralized Initializer to pre-load static references and configure cascading dropdowns
 */
async function initFormLookups() {
    // 1. Fetch lookup arrays concurrently
    const [batchesRes, majorsRes, provinceRes] = await Promise.all([
        ApiService.request(CONFIG.API_LOOKUPS.batches),
        ApiService.request(CONFIG.API_LOOKUPS.majors),
        ApiService.request(CONFIG.API_LOOKUPS.provinces)
    ]);

    if (!batchesRes.error) fillSelectOptions(document.getElementById('academic_batch_id'), batchesRes.data?.data || batchesRes.data);
    if (!majorsRes.error) fillSelectOptions(document.getElementById('academic_major_id'), majorsRes.data?.data || majorsRes.data);

    // 2. Safely populate student-only province selector components
    const provinceList = provinceRes.data?.data || provinceRes.data || [];
    fillSelectOptions(document.getElementById('student_province'), provinceList);

    // 3. Set up the cascading listener links for the student's address fields
    setupCascadeListener('student_province', 'student_district', CONFIG.API_LOOKUPS.districts, 'ស្រុក');
    setupCascadeListener('student_district', 'student_commune', CONFIG.API_LOOKUPS.communes, 'ឃុំ');
    setupCascadeListener('student_commune', 'student_village', CONFIG.API_LOOKUPS.villages, 'ភូមិ');
}

/**
 * Reusable abstract layer to handle dynamic parent-to-child select lookups
 */
function setupCascadeListener(parentId, childId, apiEndpoint, labelKhmer) {
    const parentEl = document.getElementById(parentId);
    const childEl = document.getElementById(childId);
    if (!parentEl || !childEl) return;

    parentEl.addEventListener('change', async function() {
        const apiLookupId = this.value; // Safely reads the database numeric ID directly

        childEl.innerHTML = `<option value="" disabled selected>-- ជ្រើសរើស${labelKhmer} --</option>`;
        childEl.disabled = true;

        const prefix = parentId.startsWith('student_') ? 'student_' : 'guardian_';
        if (childId === `${prefix}district`) {
            resetSelectField(`${prefix}commune`, 'ឃុំ');
            resetSelectField(`${prefix}village`, 'ភូមិ');
        } else if (childId === `${prefix}commune`) {
            resetSelectField(`${prefix}village`, 'ភូមិ');
        }

        if (!apiLookupId) return;

        const { error, data } = await ApiService.request(`${apiEndpoint}/${apiLookupId}`);
        if (!error) {
            const items = data?.data || data || [];
            fillSelectOptions(childEl, items, 'id', 'name');
            childEl.disabled = false;
        }
    });
}

/**
 * Reusable utility helper to force lock and clear an element field state
 */
function resetSelectField(elementId, labelKhmer) {
    const el = document.getElementById(elementId);
    if (el) {
        el.innerHTML = `<option value="" disabled selected>-- ជ្រើសរើស${labelKhmer} --</option>`;
        el.disabled = true;
    }
}
// 7. INITIALIZE PIPELINE HANDLER
function init() {
    // Expose toggleModal globally so your HTML buttons can read AppModal.toggle(true)
    window.AppModal = {
        toggle: (open) => toggleModal(open)
    };

    // Bind Form Submission Listener
    DOM.form?.addEventListener('submit', handleFormSubmit);

    // Bind Search Debounce Listener
    DOM.searchInput?.addEventListener('input', (e) => {
        clearTimeout(state.debounceTimer);
        state.debounceTimer = setTimeout(() => loadstudents(e.target.value), CONFIG.DEBOUNCE_DELAY);
    });

    // Fire lookups to populate Batch, Major, and Provinces dropdown options
    initFormLookups();
}

// Ensure init runs once the DOM is ready
document.addEventListener('DOMContentLoaded', init);
document.addEventListener('DOMContentLoaded', init);



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
        editingstudentId: null,
        debounceTimer: null
    };

// 4. CORE API SERVICES
const ApiService = {
    async request(url, options = {}) {
        this.toggleLoader(true);
        try {
            const { headers, method = 'GET', body, ...restOptions } = options;
            const response = await fetch(url, {
                method: method,
                credentials: 'omit',
                headers: {
                    'Accept': 'application/json',
                    ...headers
                },
                body: body,
                ...restOptions
            });

            const contentType = response.headers.get("content-type");
            const isJson = contentType && contentType.includes("application/json");
            const result = isJson ? await response.json() : null;

            return { error: !response.ok, status: response.status, data: result };
        } catch (err) {
            console.error(`[API Error] Action failed on ${url}:`, err);
            return { error: true, status: 500, data: null };
        } finally {
            this.toggleLoader(false);
        }
    },
    toggleLoader(show) {
        if (DOM.loader) DOM.loader.classList.toggle('hidden', !show);
    }
};
window.ApiService = ApiService;

// Utility to parse nested form fields like "guardians[0][name_kh]" into clean nested JS objects
function parseNestedFormData(formElement) {
    const formData = new FormData(formElement);
    const root = {};

    for (const [key, value] of formData.entries()) {
        if (key === 'search') continue;
        const cleanVal = value.toString().trim();
        const finalVal = cleanVal === '' ? null : cleanVal;

        // Regex searches for structured fields like: guardians[0][addresses][0][street]
        const parts = key.split(/\]\[|\[|\]/).filter(p => p !== '');

        let current = root;
        for (let i = 0; i < parts.length; i++) {
            const part = parts[i];
            const isLast = i === parts.length - 1;
            const nextPart = parts[i + 1];
            const isNextAnIndex = nextPart !== undefined && !isNaN(parseInt(nextPart));

            if (isLast) {
                current[part] = finalVal;
            } else {
                if (!current[part]) {
                    current[part] = isNextAnIndex ? [] : {};
                }
                current = current[part];
            }
        }
    }
    return root;
}

// 5. CORE WORKFLOW CONTROLLERS
async function loadstudents(searchQuery = '') {
    const url = `${CONFIG.API_BASE}?search=${encodeURIComponent(searchQuery)}`;
    const { error, data } = await ApiService.request(url);
    if (error) {
        Toast.fire({ icon: 'error', title: 'Failed to load students' });
        return;
    }
    const records = data && Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
    renderTable(records);
}

async function handleEditAction(id) {
    // 1. Fetch the absolute fresh model record data from the API endpoint resource route
    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យបានទេ' });
        return;
    }

    // Adapt to either root array envelopes or default unified pagination data models
    const payload = data.data || data;

    // Set system operation states to block conflict submission flags
    state.isEditMode = true;
    state.editingstudentId = id;

    // Update form buttons structural typography states
    if (DOM.modalTitle) DOM.modalTitle.textContent = 'កែប្រែព័ត៌មានលម្អិតនិស្សិត (Update Student Profile)';
    if (DOM.submitBtn) DOM.submitBtn.textContent = 'ធ្វើបច្ចុប្បន្នភាពទិន្នន័យ (Update)';

    if (!DOM.form) return;

    // ==========================================
    // 2. POPULATE ACADEMIC DETAILS (Student Model Core)
    // ==========================================
    ['code', 'status', 'batch_id', 'major_id', 'admission_at', 'bacc_2_code', 'entrance_exam', 'exit_exam'].forEach(field => {
        const el = DOM.form.querySelector(`[name="${field}"]`);
        if (el) el.value = payload[field] ?? '';
    });


    // ==========================================
    // 3. POPULATE STUDENT PERSONAL PROFILE & CASCADING ADDRESS
    // ==========================================
    if (payload.person) {
        ['name_kh', 'name_en', 'gender', 'dob', 'phone', 'email'].forEach(field => {
            const el = DOM.form.querySelector(`[name="${field}"]`);
            if (el) el.value = payload.person[field] ?? '';
        });

        // Evaluate and map Student Address Dynamic Lookup Array
        if (Array.isArray(payload.person.addresses) && payload.person.addresses.length > 0) {
            const addr = payload.person.addresses[0];

            // Map standard text properties
            const streetEl = DOM.form.querySelector(`[name="addresses[0][street]"]`);
            if (streetEl) streetEl.value = addr.street ?? '';

            // Set root province select element value attribute
            const provSelect = document.getElementById('student_province');
            if (provSelect && addr.province_id) {
                provSelect.value = addr.province_id;

                // Step A: Load and auto-select districts dataset via binding parameters
                const distSelect = document.getElementById('student_district');
                const distRes = await ApiService.request(`${CONFIG.API_LOOKUPS.districts}/${addr.province_id}`);
                if (!distRes.error) {
                    fillSelectOptions(distSelect, distRes.data?.data || distRes.data, 'id', 'name');
                    distSelect.value = addr.district ?? '';
                    distSelect.disabled = false;
                }

                // Step B: Load and auto-select communes dataset based on resolved district value
                const commSelect = document.getElementById('student_commune');
                const commRes = await ApiService.request(`${CONFIG.API_LOOKUPS.communes}/${addr.district}`);
                if (!commRes.error) {
                    fillSelectOptions(commSelect, commRes.data?.data || commRes.data, 'id', 'name');
                    commSelect.value = addr.commune ?? '';
                    commSelect.disabled = false;
                }

                // Step C: Load and auto-select ultimate target villages options array
                const villSelect = document.getElementById('student_village');
                const villRes = await ApiService.request(`${CONFIG.API_LOOKUPS.villages}/${addr.commune}`);
                if (!villRes.error) {
                    fillSelectOptions(villSelect, villRes.data?.data || villRes.data, 'id', 'name');
                    villSelect.value = addr.village ?? '';
                    villSelect.disabled = false;
                }
            }
        }
        AppModal.toggle(true);
    }


    // ==========================================
    // 4. POPULATE GUARDIAN CONTROLS & ASSOCIATED RELATIONSHIPS
    // ==========================================
    if (Array.isArray(payload.guardians) && payload.guardians.length > 0) {
        const guard = payload.guardians[0];

        // Populate system pivot table attributes managed by intermediate Eloquent variables
        const relEl = DOM.form.querySelector(`[name="guardians[0][relationship]"]`);
        if (relEl && guard.pivot) relEl.value = guard.pivot.relationship ?? '';

        const primEl = DOM.form.querySelector(`[name="guardians[0][is_primary]"]`);
        if (primEl && guard.pivot) primEl.value = guard.pivot.is_primary ?? '0';

        const occEl = DOM.form.querySelector(`[name="guardians[0][occupation]"]`);
        if (occEl) occEl.value = guard.occupation ?? '';

        // Extract guardian personal profile details from relation block nested sub-tree
        if (guard.person) {
            ['name_kh', 'name_en', 'gender', 'dob', 'phone'].forEach(field => {
                const el = DOM.form.querySelector(`[name="guardians[0][${field}]"]`);
                if (el) el.value = guard.person[field] ?? '';
            });

            // Map and resolve guardian independent layout address tree options dependencies
            if (Array.isArray(guard.person.addresses) && guard.person.addresses.length > 0) {
                const gAddr = guard.person.addresses[0];

                const gStreetEl = DOM.form.querySelector(`[name="guardians[0][addresses][0][street]"]`);
                if (gStreetEl) gStreetEl.value = gAddr.street ?? '';

                const gProvSelect = document.getElementById('guardian_province');
                if (gProvSelect && gAddr.province_id) {
                    gProvSelect.value = gAddr.province_id;

                    // Step A: Load Guardian Districts options list mappings
                    const gDistSelect = document.getElementById('guardian_district');
                    const gDistRes = await ApiService.request(`${CONFIG.API_LOOKUPS.districts}/${gAddr.province_id}`);
                    if (!gDistRes.error) {
                        fillSelectOptions(gDistSelect, gDistRes.data?.data || gDistRes.data, 'id', 'name');
                        gDistSelect.value = gAddr.district ?? '';
                        gDistSelect.disabled = false;
                    }

                    // Step B: Load Guardian Communes options data layout variables
                    const gCommSelect = document.getElementById('guardian_commune');
                    const gCommRes = await ApiService.request(`${CONFIG.API_LOOKUPS.communes}/${gAddr.district}`);
                    if (!gCommRes.error) {
                        fillSelectOptions(gCommSelect, gCommRes.data?.data || gCommRes.data, 'id', 'name');
                        gCommSelect.value = gAddr.commune ?? '';
                        gCommSelect.disabled = false;
                    }

                    // Step C: Load Guardian Villages options lists
                    const gVillSelect = document.getElementById('guardian_village');
                    const gVillRes = await ApiService.request(`${CONFIG.API_LOOKUPS.villages}/${gAddr.commune}`);
                    if (!gVillRes.error) {
                        fillSelectOptions(gVillSelect, gVillRes.data?.data || gVillRes.data, 'id', 'name');
                        gVillSelect.value = gAddr.village ?? '';
                        gVillSelect.disabled = false;
                    }
                }
            }
        }
    }

    // Force focus reset navigation view targets directly back to structural tab page 1
    window.switchTab('identity');

    // Open the container modal shell layout display components
    AppModal(true);
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
            loadstudents(DOM.searchInput?.value || '');
        } else {
            Toast.fire({ icon: 'error', title: 'មានបញ្ហាមិនអាចលុបទិន្នន័យនេះបាន' });
        }

    }

async function handleFormSubmit(e) {
    e.preventDefault();
    if (!DOM.form || !DOM.submitBtn) return;

    // 1. Reset all previous red validation state rings across the form
    DOM.form.querySelectorAll('input, select, textarea').forEach(element => {
        element.classList.remove('border-rose-500', 'ring-4', 'ring-rose-500/20', 'dark:border-rose-500');
    });

    // 2. Identify all invalid required fields in the form container
    const invalidFields = DOM.form.querySelectorAll(':invalid');
    if (invalidFields.length > 0) {
        // Highlight EVERY invalid input element with a clean red border styling
        invalidFields.forEach(field => {
            field.classList.add('border-rose-500', 'ring-4', 'ring-rose-500/20', 'dark:border-rose-500');
        });

        // Pull out the absolute first invalid element to refocus the view layout
        const firstInvalidField = invalidFields[0];
        const closestPanel = firstInvalidField.closest('.tab-panel');

        if (closestPanel) {
            const tabId = closestPanel.id.replace('panel-', '');
            window.switchTab(tabId); // Instantly jump to the tab containing the error
        }

        Toast.fire({
            icon: 'warning',
            title: 'សូមបំពេញព័ត៌មានដែលចាំបាច់',
            text: 'សូមពិនិត្យមើលវាលទិន្នន័យដែលបានចំណាំព័ទ្ធជុំវិញពណ៌ក្រហម។'
        });

        setTimeout(() => firstInvalidField.focus(), 150);
        return;
    }

    // 3. Validation passed successfully -> Submit data payload
    DOM.submitBtn.disabled = true;
    const payload = parseNestedFormData(DOM.form);

    const url = state.isEditMode ? `${CONFIG.API_BASE}/${state.editingstudentId}` : CONFIG.API_BASE;
    const method = state.isEditMode ? 'PUT' : 'POST';

    const { error, status, data } = await ApiService.request(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    if (!error) {
        Toast.fire({
            icon: 'success',
            title: state.isEditMode ? 'ធ្វើបច្ចុប្បន្នភាពជោគជ័យ!' : 'បង្កើតគណនីនិស្សិតជោគជ័យ!'
        });
        toggleModal(false);
        loadstudents();
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
function renderTable(students) {
    if (!DOM.tableBody) return;

    if (!students || students.length === 0) {
        DOM.tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-10 text-neutral-500">រកមិនឃើញទិន្នន័យនិស្សិតទេ (No student records found).</td></tr>';
        return;
    }

    // Single column stacked cards on mobile, real table from md: up
    DOM.tableBody.className = "grid grid-cols-1 gap-3 p-4 md:p-0 md:table-row-group md:gap-0 md:divide-y md:divide-neutral-200 md:dark:divide-white/5";

    DOM.tableBody.innerHTML = students.map((student, index) => {
        const person = student.person || {};
        const major = student.major || {};
        const batch = student.batch || {};

        const nameKhmer = person.first_name_kh || person.last_name_kh
            ? `${person.last_name_kh} ${person.first_name_kh}`.trim()
            : '<span class="text-neutral-400 italic">គ្មានទិន្នន័យ</span>';

        const nameEnglish = person.first_name || person.last_name
            ? `${person.last_name} ${person.first_name}`.trim()
            : '<span class="text-neutral-400 italic">N/A</span>';

        const dobFormatted = person.dob
            ? new Date(person.dob).toLocaleDateString(CONFIG.LOCALE, { day: '2-digit', month: 'short', year: 'numeric' })
            : '<span class="text-neutral-400 italic">Unknown</span>';

        const officialDateFormatted = student.admission_at
            ? new Date(student.admission_at).toLocaleDateString(CONFIG.LOCALE, { day: '2-digit', month: 'short', year: 'numeric' })
            : '<span class="text-neutral-400 italic">Not set</span>';

        let sexLabel = '<span class="text-neutral-400">-</span>';
        if (person.sex === 'male') sexLabel = '<span class="font-medium text-neutral-800 dark:text-neutral-200">ប្រុស (M)</span>';
        if (person.sex === 'female') sexLabel = '<span class="font-medium text-pink-600 dark:text-pink-400">ស្រី (F)</span>';
        if (person.sex === 'other') sexLabel = '<span class="font-medium text-neutral-500">ផ្សេងៗ</span>';

        const statusValue = (student.status || 'active').toLowerCase();
        let statusBadgeClasses = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400';
        let statusTextKhmer = 'សិក្សា';
        if (statusValue === 'suspended' || statusValue === 'dropped') {
            statusBadgeClasses = 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400';
            statusTextKhmer = 'បោះបង់';
        } else if (statusValue === 'graduated') {
            statusBadgeClasses = 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400';
            statusTextKhmer = 'បញ្ចប់ការសិក្សា';
        }

        // Initials for the avatar circle (mobile header)
        const initialsSource = (person.first_name_kh || person.first_name || student.code || '?').toString();
        const initials = initialsSource.trim().charAt(0).toUpperCase();

        return `
            <tr class="block md:table-row bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm md:shadow-none md:border-0 md:border-b md:rounded-none overflow-hidden md:overflow-visible">

                <td class="hidden md:table-cell px-6 py-4 text-neutral-400 font-mono text-xs">${index + 1}</td>

                <!-- ============ MOBILE CARD HEADER (hidden on md+) ============ -->
                <td class="block md:hidden p-0">
                    <div class="flex items-center gap-3 p-4 border-b border-neutral-100 dark:border-white/5">
                        <div class="shrink-0 w-11 h-11 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                            ${initials}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-neutral-900 dark:text-neutral-100 text-[15px] leading-tight truncate">${nameKhmer}</div>
                            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-medium truncate">${nameEnglish}</div>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-2 py-1 text-[10px] font-bold rounded-full whitespace-nowrap ${statusBadgeClasses}">
                            ${statusTextKhmer}
                        </span>
                    </div>

                    <!-- Compact 2-column fact grid -->
                    <div class="grid grid-cols-2 gap-x-3 gap-y-2.5 px-4 py-3 text-xs">
                        <div>
                            <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Student ID</span>
                            <span class="font-mono font-bold text-neutral-800 dark:text-neutral-200">${student.code ?? '<span class="text-rose-500 italic font-normal">Missing</span>'}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Sex</span>
                            ${sexLabel}
                        </div>
                        <div>
                            <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">DOB</span>
                            <span class="font-mono text-neutral-600 dark:text-neutral-400">${dobFormatted}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Admission</span>
                            <span class="font-mono text-neutral-600 dark:text-neutral-400">${officialDateFormatted}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide block">Academic Plan</span>
                            <span class="font-semibold text-neutral-800 dark:text-neutral-200">${major.name ?? 'No Major Assigned'}</span>
                            <span class="text-neutral-400"> · Batch ${batch.name ?? 'N/A'}</span>
                        </div>
                    </div>

                    <!-- Bigger, clearer touch-friendly actions -->
                    <div class="grid grid-cols-3 border-t border-neutral-100 dark:border-white/5">
                        <button data-action="preview" data-id="${student.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-indigo-600 active:bg-indigo-50 dark:active:bg-indigo-500/10">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview
                        </button>
                        <button data-action="edit" data-id="${student.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-amber-600 border-l border-r border-neutral-100 dark:border-white/5 active:bg-amber-50 dark:active:bg-amber-500/10">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <button data-action="delete" data-id="${student.id}" class="flex items-center justify-center gap-1.5 py-3 text-xs font-medium text-rose-600 active:bg-rose-50 dark:active:bg-rose-500/10">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </div>
                </td>

                <!-- ============ DESKTOP TABLE CELLS (hidden below md) ============ -->
                <td class="hidden md:table-cell px-6 py-4">
                    <div class="flex flex-col gap-0.5">
                        <span class="font-bold text-neutral-900 dark:text-neutral-100 text-sm font-sans">${nameKhmer}</span>
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">${nameEnglish}</span>
                    </div>
                </td>

                <td class="hidden md:table-cell px-6 py-4 font-mono text-sm text-neutral-800 dark:text-neutral-200 font-bold">
                    ${student.code ?? '<span class="text-rose-500 italic text-xs font-normal">Missing Code</span>'}
                </td>

                <td class="hidden md:table-cell px-6 py-4 text-sm">${sexLabel}</td>

                <td class="hidden md:table-cell px-6 py-4 text-xs font-mono text-neutral-600 dark:text-neutral-400">${dobFormatted}</td>

                <td class="hidden md:table-cell px-6 py-4">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">${major.name ?? 'No Major Assigned'}</span>
                        <span class="text-xs text-neutral-400 font-mono">Batch: ${batch.name ?? 'N/A'}</span>
                    </div>
                </td>

                <td class="hidden md:table-cell px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full ${statusBadgeClasses}">
                        ${statusTextKhmer} (${statusValue})
                    </span>
                </td>

                <td class="hidden md:table-cell px-6 py-4 text-xs font-mono text-neutral-500">${officialDateFormatted}</td>

                <td class="hidden md:table-cell p-6 text-right">
                    <div class="flex justify-end gap-1.5">
                        <button data-action="preview" data-id="${student.id}" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-xl transition-colors" title="Preview Full Student Profile">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button data-action="edit" data-id="${student.id}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-xl transition-colors" title="Edit student">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button data-action="delete" data-id="${student.id}" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors" title="Delete student">
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
function togglePreviewModal(show) {
    const el = document.getElementById('previewModal');
    if (!el) return;
    if (show) {
        el.classList.remove('hidden');
        setTimeout(() => el.firstElementChild.classList.remove('scale-95'), 10);
    } else {
        el.firstElementChild.classList.add('scale-95');
        setTimeout(() => el.classList.add('hidden'), 150);
    }
}

// CRITICAL: Expose it to the window scope so your HTML buttons can call it!
window.togglePreviewModal = togglePreviewModal;


    function resetFormState() {
        if (DOM.form) DOM.form.reset();
        state.isEditMode = false;
        state.editingstudentId = null;

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
                loadstudents(e.target.value);
            }, CONFIG.DEBOUNCE_DELAY);
        });

        // Form Submit
        DOM.form?.addEventListener('submit', handleFormSubmit);

        // Update the table body listener inside your initEvents() function exactly like this:
        DOM.tableBody?.addEventListener('click', (e) => {
            const targetBtn = e.target.closest('button[data-action]');
            if (!targetBtn) return;

            const action = targetBtn.getAttribute('data-action');
            const targetId = targetBtn.getAttribute('data-id');

            // These will now match perfectly inside the exact same scope
            if (action === 'preview') handleLocalPreviewAction(targetId);
            if (action === 'edit') handleEditAction(targetId);
            if (action === 'delete') handleDeleteAction(targetId);
        });

        // Interactive Tooltips Hint Dynamic Engine
        const inputsWithHints = document.querySelectorAll('#addstudentForm input, #addstudentForm textarea');
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
        loadstudents();
    });
})();


/**
 * Tab Switching Controller Utility
 * Explicitly bound to the global window scope to prevent Uncaught ReferenceErrors
 */
window.switchTab = function(tabId) {
    // 1. Hide all panels safely
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });

    // 2. Reset styling on all tab button toggles
    document.querySelectorAll('[id^="tab-"]').forEach(button => {
        button.classList.remove('bg-white', 'dark:bg-neutral-900', 'text-indigo-600', 'dark:text-white', 'shadow-sm', 'font-bold');
        button.classList.add('text-neutral-500', 'dark:text-neutral-400', 'font-medium');
    });

    // 3. Locate and highlight active target panel and button components
    const targetPanel = document.getElementById(`panel-${tabId}`);
    const targetTab = document.getElementById(`tab-${tabId}`);

    if (targetPanel && targetTab) {
        // Reveal active panel section
        targetPanel.classList.remove('hidden');

        // Apply active design styles to the button
        targetTab.classList.remove('text-neutral-500', 'dark:text-neutral-400', 'font-medium');
        targetTab.classList.add('bg-white', 'dark:bg-neutral-900', 'text-indigo-600', 'dark:text-white', 'shadow-sm', 'font-bold');
    }
};

/**
 * Localized Profile Preview Engine
 */
async function handleLocalPreviewAction(id) {
    // 1. Toggle visibility of the modal frame wrapper
    const modalEl = document.getElementById('previewModal');
    if (modalEl) {
        modalEl.classList.remove('hidden');
        setTimeout(() => modalEl.firstElementChild?.classList.remove('scale-95'), 10);
    }

    const contentContainer = document.getElementById('previewModalContent');
    if (!contentContainer) return;

    // 2. Render localized loading state placeholder animation
    contentContainer.innerHTML = `
        <div class="flex flex-col items-center justify-center py-20 gap-3">
            <div class="w-8 h-8 rounded-full border-2 border-indigo-600 border-t-transparent animate-spin"></div>
            <span class="text-xs text-neutral-400">កំពុងទាញយកទិន្នន័យគ្រប់ជ្រុងជ្រោយ...</span>
        </div>`;

    // 3. Make request using your native local ApiService & CONFIG
    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
    if (error) {
        contentContainer.innerHTML = `<p class="text-center text-rose-500 py-10">បរាជ័យក្នុងការទាញយកទិន្នន័យ។</p>`;
        return;
    }

    const student = data.data || data;
    const person = student.person || {};
    const major = student.major || {};
    const batch = student.batch || {};
    const address = Array.isArray(person.addresses) && person.addresses.length > 0 ? person.addresses[0] : null;
    const guardian = Array.isArray(student.guardians) && student.guardians.length > 0 ? student.guardians[0] : null;

    // 4. Local formatting rules using your system local presets
    const nameKhmer = person.first_name_kh || person.last_name_kh ? `${person.last_name_kh} ${person.first_name_kh}`.trim() : 'N/A';
    const nameEnglish = person.first_name || person.last_name ? `${person.last_name} ${person.first_name}`.trim() : 'N/A';

    const dobFormatted = person.dob
        ? new Date(person.dob).toLocaleDateString(CONFIG.LOCALE || 'km-KH', { day: '2-digit', month: 'short', year: 'numeric' })
        : 'N/A';
    const admissionFormatted = student.admission_at
        ? new Date(student.admission_at).toLocaleDateString(CONFIG.LOCALE || 'km-KH', { day: '2-digit', month: 'short', year: 'numeric' })
        : 'N/A';

    // 5. Inject comprehensive read-only payload layout
    contentContainer.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
                <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">១. អត្តសញ្ញាណផ្ទាល់ខ្លួន (Personal Identity)</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
                    <div><span class="text-neutral-400 block">គោត្តនាម-នាមខ្លួន (KH):</span> <span class="font-bold text-neutral-900 dark:text-white">${nameKhmer}</span></div>
                    <div><span class="text-neutral-400 block">Full Name (EN):</span> <span class="font-bold text-neutral-900 dark:text-white">${nameEnglish}</span></div>
                    <div><span class="text-neutral-400 block">ភេទ (Sex):</span> <span class="capitalize font-semibold">${person.sex ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ថ្ងៃខែឆ្នាំកំណើត (DOB):</span> <span class="font-mono">${dobFormatted}</span></div>
                    <div><span class="text-neutral-400 block">លេខទូរស័ព្ទ (Phone):</span> <span class="font-mono">${person.phone ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">អ៊ីមែល (Email):</span> <span class="font-mono truncate block">${person.email ?? 'N/A'}</span></div>
                </div>
            </div>

            <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
                <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">២. ព័ត៌មានសិក្សា (Academic Records)</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
                    <div><span class="text-neutral-400 block">អត្តសញ្ញាណប័ណ្ណនិស្សិត (ID Code):</span> <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">${student.code ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ស្ថានភាពនិស្សិត (Status):</span> <span class="uppercase font-bold text-emerald-500">${student.status ?? 'Active'}</span></div>
                    <div><span class="text-neutral-400 block">ជំនាញឯកទេស (Major):</span> <span class="font-semibold">${major.name ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ជំនាន់សិក្សា (Batch):</span> <span class="font-mono">${batch.name ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">កាលបរិច្ឆេទចូលរៀន (Admission Date):</span> <span class="font-mono">${admissionFormatted}</span></div>
                    <div><span class="text-neutral-400 block">លេខតុ បាក់ឌុប (BACC II Code):</span> <span class="font-mono">${student.bacc_2_code ?? 'N/A'}</span></div>
                </div>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
            <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">៣. អាសយដ្ឋានបច្ចុប្បន្ន (Current Residential Address)</h4>
            ${address ? `
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs">
                    <div><span class="text-neutral-400 block">ផ្ទះ/ផ្លូវ (Street/House):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.street ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ខេត្ត/រាជធានី (Province):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.province?.name_kh || address.province?.name || 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ស្រុក/ខណ្ឌ (District):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.district?.name_kh ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ឃុំ/សង្កាត់ (Commune):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.commune?.name_kh ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">ភូមិ (Village):</span> <span class="font-medium text-neutral-900 dark:text-white">${address.village?.name_kh ?? 'N/A'}</span></div>
                </div>
            ` : `<p class="text-xs text-neutral-400 italic">គ្មានការកំណត់ព័ត៌មានអាសយដ្ឋានទេ</p>`}
        </div>

        <div class="bg-neutral-50 dark:bg-white/5 border border-neutral-100 dark:border-white/5 rounded-xl p-5 space-y-3">
            <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider border-b border-neutral-200 dark:border-white/10 pb-2">៤. ព័ត៌មានអាណាព្យាបាល (Primary Guardian Relations)</h4>
            ${guardian ? `
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div><span class="text-neutral-400 block">ឈ្មោះខ្មែរ (Name KH):</span> <span class="font-bold text-neutral-900 dark:text-white">${guardian.person?.last_name_kh ?? ''} ${guardian.person?.first_name_kh ?? ''}</span></div>
                    <div><span class="text-neutral-400 block">Name EN:</span> <span class="font-semibold text-neutral-900 dark:text-white">${guardian.person?.last_name ?? ''} ${guardian.person?.first_name ?? ''}</span></div>
                    <div><span class="text-neutral-400 block">ត្រូវជា (Relationship):</span> <span class="font-medium text-indigo-600 dark:text-indigo-400">${guardian.pivot?.relationship ?? 'N/A'}</span></div>
                    <div><span class="text-neutral-400 block">លេខទូរស័ព្ទ (Phone):</span> <span class="font-mono font-medium">${guardian.person?.phone ?? 'N/A'}</span></div>
                </div>
            ` : `<p class="text-xs text-neutral-400 italic">គ្មានទិន្នន័យអាណាព្យាបាលភ្ជាប់ជាមួយឡើយ</p>`}
        </div>`;
}

// Global modal close intercept helper mapping
window.closePreviewModalWrapper = function() {
    const el = document.getElementById('previewModal');
    if (!el) return;
    el.firstElementChild?.classList.add('scale-95');
    setTimeout(() => el.classList.add('hidden'), 150);
};

