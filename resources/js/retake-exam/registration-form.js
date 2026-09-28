import { CONFIG } from './config.js';
import { state, openModal, closeModal, Toast } from './core.js';

/**
 * Add / edit one registration by hand. The student, subject and lecturer
 * fields are type-to-search inputs backed by a <datalist> (same pattern as
 * class/classModal) — but filled per keystroke from
 * /retake-registrations-options instead of preloading every row, since
 * there are thousands of students and REG can't read the students/
 * subjects/lecturers modules directly anyway.
 */

let editingId = null;
const pickers = {};

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

function lookupLabel(code, name) {
    return code && name ? `${code} — ${name}` : (code || name || '');
}

/**
 * One searchable picker: visible text input + datalist + hidden id. The id
 * is only set when the typed text exactly matches an option the server
 * returned — free text never silently becomes a selection.
 */
function createPicker(key, type) {
    const input = document.getElementById(`retakeReg${key}Search`);
    const list = document.getElementById(`retakeReg${key}List`);
    const idInput = document.getElementById(`retakeReg${key}Id`);
    const hint = document.getElementById(`retakeReg${key}Hint`);
    const labelToId = new Map();
    let timer = null;
    let controller = null;

    const syncId = () => {
        const id = labelToId.get(input.value);
        idInput.value = id ?? '';
        hint?.classList.add('hidden');
    };

    // Plain fetch, not ApiService — that one flashes the full-page loading
    // overlay, which would blink on every keystroke here.
    const fetchOptions = async (search) => {
        controller?.abort();
        controller = new AbortController();
        const params = new URLSearchParams({ type, search });
        let data;
        try {
            const res = await fetch(`${CONFIG.REGISTRATION_OPTIONS_API}?${params}`, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
                signal: controller.signal,
            });
            if (!res.ok) return;
            data = await res.json();
        } catch {
            return; // aborted by a newer keystroke, or offline
        }

        const items = Array.isArray(data?.data) ? data.data : [];
        items.forEach((item) => labelToId.set(item.label, item.id));
        // Built via DOM, not an HTML string — labels are student/lecturer
        // names and can contain quotes.
        list.replaceChildren(...items.map((item) => {
            const option = document.createElement('option');
            option.value = item.label;
            return option;
        }));
        syncId();
    };

    input.addEventListener('input', () => {
        syncId();
        // Picking an option from the datalist fires 'input' with the full
        // label — no need to search again for what was just chosen.
        if (idInput.value) return;
        clearTimeout(timer);
        timer = setTimeout(() => fetchOptions(input.value.trim()), CONFIG.DEBOUNCE_DELAY);
    });

    // Show the first 20 suggestions as soon as the field is focused, so
    // REG can browse without typing first.
    input.addEventListener('focus', () => {
        if (!list.children.length && !idInput.value) fetchOptions(input.value.trim());
    });

    return {
        input,
        idInput,
        hint,
        set(id, label) {
            if (id && label) labelToId.set(label, id);
            input.value = label || '';
            idInput.value = id || '';
            list.innerHTML = '';
            hint?.classList.add('hidden');
        },
        // Typed something but never picked a match.
        isUnresolved() {
            return input.value.trim() !== '' && !idInput.value;
        },
    };
}

export function initRegistrationForm() {
    pickers.student = createPicker('Student', 'student');
    pickers.subject = createPicker('Subject', 'subject');
    pickers.lecturer = createPicker('Lecturer', 'lecturer');
}

function fillBatchSelect(dom, selectedId, lockToSelected) {
    // Only open batches accept new rows; on edit the row's own batch is
    // shown (even if closed) but the select is locked.
    const batches = lockToSelected
        ? state.batches.filter((b) => String(b.id) === String(selectedId))
        : state.batches.filter((b) => b.status !== 'closed');

    dom.regBatchSelect.innerHTML = '<option value="" disabled selected>-- ជ្រើសរើសជំនាន់ (Select batch) --</option>' +
        batches.map((b) => {
            const label = `${b.exam_type?.name_en || b.exam_type?.code || '—'} · ${b.term?.title || 'No term'} (#${b.id})`;
            return `<option value="${b.id}">${escapeHtml(label)}</option>`;
        }).join('');

    if (lockToSelected && batches.length === 0 && selectedId) {
        dom.regBatchSelect.innerHTML = `<option value="${selectedId}">Batch #${selectedId}</option>`;
    }

    if (selectedId) dom.regBatchSelect.value = String(selectedId);
    else if (state.filters.batch_id && batches.some((b) => String(b.id) === String(state.filters.batch_id))) {
        // Default to the batch chip currently selected in the strip.
        dom.regBatchSelect.value = String(state.filters.batch_id);
    }

    dom.regBatchSelect.disabled = lockToSelected;
    dom.regBatchHint.classList.toggle('hidden', !lockToSelected);
}

export function openCreateRegistration(dom) {
    editingId = null;
    dom.regModalTitle.textContent = 'បន្ថែមនិស្សិត (Add Student)';
    fillBatchSelect(dom, null, false);
    pickers.student.set(null, '');
    pickers.subject.set(null, '');
    pickers.lecturer.set(null, '');
    dom.regRemark.value = '';

    if (dom.regBatchSelect.options.length <= 1) {
        Toast.fire({ icon: 'warning', title: 'មិនមានជំនាន់បើកទេ (No open batch — import or create one first)' });
        return;
    }
    openModal(dom.regModal);
}

export function openEditRegistration(dom, row) {
    editingId = row.id;
    dom.regModalTitle.textContent = 'កែប្រែការចុះឈ្មោះ (Edit Registration)';
    fillBatchSelect(dom, row.batch_id, true);

    const student = row.student ?? {};
    const subject = row.subject ?? {};
    const lecturer = row.lecturer ?? {};
    pickers.student.set(student.id, lookupLabel(student.code, student.name || student.name_en));
    pickers.subject.set(subject.id, lookupLabel(subject.code, subject.name_en || subject.name));
    pickers.lecturer.set(lecturer.id, lookupLabel(lecturer.code, lecturer.name_en || lecturer.name));
    dom.regRemark.value = row.remark ?? '';

    openModal(dom.regModal);
}

export async function submitRegistrationForm(dom, ApiService, onDone) {
    let invalid = false;
    ['student', 'subject'].forEach((key) => {
        if (!pickers[key].idInput.value) {
            pickers[key].hint?.classList.remove('hidden');
            invalid = true;
        }
    });
    if (pickers.lecturer.isUnresolved()) {
        pickers.lecturer.hint?.classList.remove('hidden');
        invalid = true;
    }
    if (invalid) return;

    const payload = {
        batch_id: Number(dom.regBatchSelect.value),
        student_id: Number(pickers.student.idInput.value),
        subject_id: Number(pickers.subject.idInput.value),
        lecturer_id: pickers.lecturer.idInput.value ? Number(pickers.lecturer.idInput.value) : null,
        remark: dom.regRemark.value.trim() || null,
    };

    dom.regSubmitBtn.disabled = true;
    const url = editingId ? `${CONFIG.REGISTRATIONS_API}/${editingId}` : CONFIG.REGISTRATIONS_API;
    const { error, data } = await ApiService.request(url, {
        method: editingId ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
    dom.regSubmitBtn.disabled = false;

    if (error) {
        const firstFieldError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstFieldError || data?.message || 'មិនអាចរក្សាទុកបានទេ (Could not save)' });
        return;
    }

    Toast.fire({ icon: 'success', title: editingId ? 'កែប្រែជោគជ័យ! (Updated)' : 'បន្ថែមជោគជ័យ! (Added)' });
    closeModal(dom.regModal);
    onDone();
}
