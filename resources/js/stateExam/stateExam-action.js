import { CONFIG } from './config.js';
import { state, Toast } from './core.js';
import { loadStateExam } from './stateExam-list.js';
import { loadMajorsRows, resetMajorsRows, collectMajorsRows } from './stateExam-majors.js';

/**
 * Opens the modal pre-filled for editing an existing exam room.
 */
export async function handleEditAction(dom, ApiService, id) {
    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យបានទេ' });
        return;
    }

    const room = data.data || data;

    state.isEditMode = true;
    state.editingId = id;

    if (dom.modalTitle) dom.modalTitle.textContent = 'កែប្រែបន្ទប់ប្រឡង (Edit Exam Room)';

    ['room', 'shift', 'major', 'degree', 'exam_date', 'remark'].forEach((field) => {
        const el = dom.form?.querySelector(`[name="${field}"]`);
        if (el) el.value = room[field] ?? '';
    });
    loadMajorsRows(dom, room.majors);

    dom.modal?.classList.remove('invisible', 'opacity-0');
    dom.modal?.classList.add('flex');
    requestAnimationFrame(() => {
        dom.modalCard?.classList.remove('scale-90', 'opacity-0');
        dom.modalCard?.classList.add('scale-100', 'opacity-100');
    });
}

/**
 * Confirms and deletes an exam room record.
 */
export async function handleDeleteAction(dom, ApiService, id) {
    const confirmation = await Swal.fire({
        title: 'តើអ្នកប្រាកដជាចង់លុបមែនទេ?',
        text: 'ទិន្នន័យនេះមិនអាចយកមកវិញបានឡើយ!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'បាទ/ចាស លុបវា!',
        cancelButtonText: 'បោះបង់',
    });

    if (!confirmation.isConfirmed) return;

    const { error } = await ApiService.request(`${CONFIG.API_BASE}/${id}`, {
        method: 'DELETE',
    });

    if (!error) {
        Toast.fire({ icon: 'success', title: 'លុបទិន្នន័យបានជោគជ័យ!' });
        loadStateExam(dom, ApiService, dom.searchInput?.value || '');
    } else {
        Toast.fire({ icon: 'error', title: 'មានបញ្ហាមិនអាចលុបទិន្នន័យនេះបាន' });
    }
}

/**
 * Validates and submits the create/edit form.
 */
export async function handleFormSubmit(dom, ApiService, e) {
    e.preventDefault();
    if (!dom.form || !dom.submitBtn) return;

    const invalidFields = dom.form.querySelectorAll(':invalid');
    if (invalidFields.length > 0) {
        Toast.fire({ icon: 'warning', title: 'សូមបំពេញព័ត៌មានដែលចាំបាច់' });
        invalidFields[0].focus();
        return;
    }

    dom.submitBtn.disabled = true;

    const formData = new FormData(dom.form);
    const payload = Object.fromEntries(formData.entries());

    const majors = collectMajorsRows(dom);
    payload.majors = majors;
    payload.student_total = majors.reduce((sum, m) => sum + m.total, 0);

    const url = state.isEditMode
        ? `${CONFIG.API_BASE}/${state.editingId}`
        : CONFIG.API_BASE;
    const method = state.isEditMode ? 'PUT' : 'POST';

    const { error, data } = await ApiService.request(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });

    if (!error) {
        Toast.fire({
            icon: 'success',
            title: state.isEditMode ? 'ធ្វើបច្ចុប្បន្នភាពជោគជ័យ!' : 'បង្កើតបន្ទប់ប្រឡងជោគជ័យ!',
        });
        closeStateExamModal(dom);
        loadStateExam(dom, ApiService);
    } else {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'warning', title: firstError || data?.message || 'មានបញ្ហាកើតឡើង' });
    }

    dom.submitBtn.disabled = false;
}

export function closeStateExamModal(dom) {
    dom.modal?.classList.add('opacity-0');
    dom.modalCard?.classList.remove('scale-100', 'opacity-100');
    dom.modalCard?.classList.add('scale-90', 'opacity-0');

    setTimeout(() => {
        dom.modal?.classList.add('invisible');
        dom.modal?.classList.remove('flex');
        dom.form?.reset();
        resetMajorsRows(dom);
        state.isEditMode = false;
        state.editingId = null;
        if (dom.modalTitle) dom.modalTitle.textContent = 'បន្ថែមបន្ទប់ប្រឡងថ្មី (Add Exam Room)';
    }, 300);
}

export function openStateExamModal(dom) {
    dom.modal?.classList.remove('invisible', 'opacity-0');
    dom.modal?.classList.add('flex');
    requestAnimationFrame(() => {
        dom.modalCard?.classList.remove('scale-90', 'opacity-0');
        dom.modalCard?.classList.add('scale-100', 'opacity-100');
    });
}
