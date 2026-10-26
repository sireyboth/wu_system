import { CONFIG } from './config.js';
import { state } from './state.js';
import { escapeHtml, Toast } from '../invigilator/core.js';
import { getRenderedRow } from './table-render.js';
import { gradeOf, gpaOf } from './grade.js';

const FIELDS = [
    'request_type', 'issue_no', 'name', 'surname', 'name_kh', 'dob', 'pob', 'pob_kh', 'address', 'address_kh', 'student_code',
    'sex', 'nationality', 'degree', 'major_id', 'academic_year', 'current_year', 'current_semester', 'study_status',
    'exam_date', 'date_kh', 'issue_date',
    'person_in_charge', 'person_role', 'person_in_charge_kh', 'person_role_kh',
    'entrance_exam_description', 'remark',
];

/** Program length in years — must match App\Helpers\Degree::years(). */
const programYears = (dom) => CONFIG.DEGREE_YEARS[dom.form.elements.degree.value] ?? CONFIG.YEARS;

/** Year tabs and payment boxes stop at the degree's length (Associate 2 years, Bachelor 4, …). */
export function applyDegree(dom) {
    const years = programYears(dom);
    dom.form.querySelectorAll('[data-year-tab]').forEach((tab) => tab.classList.toggle('hidden', Number(tab.dataset.yearTab) > years));
    dom.form.querySelectorAll('[data-pay-year]').forEach((box) => box.classList.toggle('hidden', Number(box.dataset.payYear) > years));
    if (state.activeYear > years) showYear(dom, 1);
}

/** Comprehensive exam fields only matter for a Provisional Certificate request. */
export function applyRequestType(dom) {
    const type = dom.form.elements.request_type.value;
    dom.form.querySelectorAll('[data-request-only]').forEach((el) => el.classList.toggle('hidden', el.dataset.requestOnly !== type));
}

export function bindRequestType(dom) {
    dom.form.elements.request_type?.addEventListener('change', () => applyRequestType(dom));
}

const paymentBoxes = (dom) => [...dom.form.querySelectorAll('input[name="payments"]')];

/** Ticks the saved semesters; hovering a box tells who verified it and when. */
function fillPayments(dom, payments = []) {
    const saved = new Map(payments.map((p) => [`${p.year}-${p.semester}`, p]));
    paymentBoxes(dom).forEach((box) => {
        const payment = saved.get(box.value);
        box.checked = Boolean(payment);
        box.closest('label').title = payment ? `Verified by ${payment.verified_by ?? '—'} · ${payment.verified_at ?? ''}` : '';
    });
}

const blockKey = (year, semester) => `${year}-${semester}`;
const subjectLabel = (s) => (s ? [s.code, s.name_en].filter(Boolean).join(' — ') : '');
const findSubject = (id) => state.subjects.find((s) => String(s.id) === String(id));

function listFor(dom, year, semester) {
    return dom.form.querySelector(`[data-course-list="${blockKey(year, semester)}"]`);
}

/** Opens the form empty (New) or pre-filled from a rendered row (Edit). */
export function openForm(dom, id = null) {
    const row = id ? getRenderedRow(id) : null;
    state.editingId = row ? row.id : null;

    dom.form.reset();
    keepOption(dom.form.elements.pob, row?.pob);
    FIELDS.forEach((name) => {
        const input = dom.form.elements[name];
        if (input && row) input.value = row[name] ?? '';
    });
    dom.form.elements.entrance_exam.checked = Boolean(row?.entrance_exam);
    dom.form.elements.comprehensive_exam.checked = Boolean(row?.comprehensive_exam);
    fillPayments(dom, row?.payments);

    dom.form.querySelectorAll('[data-course-list]').forEach((list) => (list.innerHTML = ''));
    (row?.subjects ?? []).forEach((s) => addCourseRow(dom, s.year, s.semester, s));

    if (dom.modalTitle) {
        dom.modalTitle.textContent = !row
            ? 'បង្កើតឯកសារ (New Request)'
            : row.is_draft
                ? 'បន្តសេចក្ដីព្រាង (Continue Draft)'
                : 'កែសម្រួលឯកសារ (Edit Request)';
    }

    showYear(dom, 1);
    applyDegree(dom);
    applyRequestType(dom);
    recalc(dom);
    window.TranscriptModal.toggle(true);
}

/** A value saved before the field became a dropdown (typed by hand) gets its own option so editing doesn't drop it. */
function keepOption(select, value) {
    select.querySelectorAll('option[data-legacy]').forEach((o) => o.remove());
    if (!value || [...select.options].some((o) => o.value === value)) return;
    const option = new Option(value, value);
    option.dataset.legacy = '1';
    select.add(option);
}

/** Province picked → Khmer place of birth follows (still editable). */
export function bindPlaceOfBirth(dom) {
    dom.form.elements.pob?.addEventListener('change', (e) => {
        const kh = e.target.selectedOptions[0]?.dataset.kh;
        if (kh) dom.form.elements.pob_kh.value = kh;
    });
}

/** Clones the <template> into a semester block — refuses past the per-semester limit. */
export function addCourseRow(dom, year, semester, course = {}) {
    const list = listFor(dom, year, semester);
    if (!list) return null;
    if (list.querySelectorAll('.course-row').length >= CONFIG.MAX_PER_SEMESTER) {
        Toast.fire({ icon: 'warning', title: `Max ${CONFIG.MAX_PER_SEMESTER} subjects per semester` });
        return null;
    }

    const fragment = dom.courseTemplate.content.cloneNode(true);
    const rowEl = fragment.querySelector('.course-row');
    const subject = findSubject(course.subject_id);

    field(rowEl, 'subject_id').value = course.subject_id ?? '';
    // Fall back to the saved snapshot if the subject was since deleted.
    field(rowEl, 'subject_search').value = subject ? subjectLabel(subject) : subjectLabel({ code: course.code, name_en: course.title });
    rowEl.querySelector('[data-role="subject-code"]').textContent = subject?.code || course.code || '—';
    field(rowEl, 'credit').value = course.credit ?? '';
    field(rowEl, 'score').value = course.score ?? '';
    toggleEditSubject(rowEl);

    list.appendChild(fragment);
    recalc(dom);
    return rowEl;
}

function field(rowEl, name) {
    return rowEl.querySelector(`[data-field="${name}"]`);
}

/** The pencil only shows for a row whose subject still exists in the master list. */
function toggleEditSubject(rowEl) {
    const show = state.permissions.subject_edit && Boolean(findSubject(field(rowEl, 'subject_id').value));
    const button = rowEl.querySelector('[data-action="edit-subject"]');
    button?.classList.toggle('hidden', !show);
    button?.classList.toggle('inline-flex', show);
}

/** Year tabs: only one year's two semesters visible at a time. */
export function showYear(dom, year) {
    state.activeYear = Number(year);
    dom.form.querySelectorAll('[data-year-panel]').forEach((panel) => {
        panel.classList.toggle('hidden', Number(panel.dataset.yearPanel) !== state.activeYear);
    });
    dom.form.querySelectorAll('[data-year-tab]').forEach((tab) => {
        const on = Number(tab.dataset.yearTab) === state.activeYear;
        tab.classList.toggle('bg-indigo-600', on);
        tab.classList.toggle('text-white', on);
        tab.classList.toggle('border-indigo-600', on);
        tab.classList.toggle('text-neutral-600', !on);
        tab.classList.toggle('dark:text-neutral-300', !on);
        tab.classList.toggle('border-neutral-200', !on);
        tab.classList.toggle('dark:border-white/10', !on);
    });
}

/** Grade badges, x/5 counters, add-button state, tab counts and the live totals. */
export function recalc(dom) {
    let credits = 0;
    const points = [];

    for (let year = 1; year <= CONFIG.YEARS; year++) {
        let yearCount = 0;
        for (const semester of [1, 2]) {
            const key = blockKey(year, semester);
            const rows = [...listFor(dom, year, semester).querySelectorAll('.course-row')];
            yearCount += rows.length;

            rows.forEach((rowEl) => {
                const grade = gradeOf(field(rowEl, 'score').value);
                const gradeEl = rowEl.querySelector('[data-role="grade"]');
                gradeEl.textContent = grade ? grade.letter : 'IP';
                gradeEl.classList.toggle('text-rose-600', grade?.letter === 'F');
                rowEl.querySelector('[data-role="grade-point"]').textContent = grade ? grade.point.toFixed(2) : '—';

                if (grade) {
                    const credit = Number(field(rowEl, 'credit').value) || 0;
                    credits += credit;
                    points.push([grade.point, credit]);
                }
            });

            dom.form.querySelector(`[data-block-count="${key}"]`).textContent = `${rows.length}/${CONFIG.MAX_PER_SEMESTER}`;
            dom.form.querySelector(`[data-course-empty="${key}"]`).classList.toggle('hidden', rows.length > 0);
            dom.form.querySelector(`[data-action="add-course"][data-year="${year}"][data-semester="${semester}"]`).disabled =
                rows.length >= CONFIG.MAX_PER_SEMESTER;
        }
        dom.form.querySelector(`[data-year-count="${year}"]`).textContent = yearCount;
    }

    dom.totalCredits.textContent = credits;
    dom.totalGpa.textContent = gpaOf(points).toFixed(2);
}

/* ---------- Subject search (select2-style) ---------- */

function usedSubjectIds(dom, exceptRow) {
    return new Set(
        [...dom.form.querySelectorAll('.course-row')]
            .filter((r) => r !== exceptRow)
            .map((r) => field(r, 'subject_id').value)
            .filter(Boolean),
    );
}

function renderOptions(dom, rowEl) {
    const box = rowEl.querySelector('[data-role="subject-options"]');
    const term = field(rowEl, 'subject_search').value.trim().toLowerCase();
    const used = usedSubjectIds(dom, rowEl);

    const matches = state.subjects
        .filter((s) => !term || `${s.code ?? ''} ${s.name_en ?? ''} ${s.name_kh ?? ''}`.toLowerCase().includes(term))
        .slice(0, 50);

    box.innerHTML = matches.length
        ? matches.map((s) => {
            const taken = used.has(String(s.id));
            return `<button type="button" data-subject-id="${s.id}" ${taken ? 'disabled' : ''}
                class="subject-option w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-500/10 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                <span class="font-mono text-xs text-indigo-600 dark:text-indigo-400 shrink-0">${escapeHtml(s.code || '—')}</span>
                <span class="truncate text-neutral-800 dark:text-neutral-200">${escapeHtml(s.name_en)}</span>
                ${taken ? '<span class="ms-auto text-[10px] text-neutral-400 shrink-0">added</span>' : ''}
            </button>`;
        }).join('')
        : '<div class="px-3 py-3 text-xs text-center text-neutral-400">No subject found</div>';

    box.classList.remove('hidden');
    box.dataset.active = '-1';
}

function pickSubject(dom, rowEl, subject) {
    field(rowEl, 'subject_id').value = subject.id;
    field(rowEl, 'subject_search').value = subjectLabel(subject);
    rowEl.querySelector('[data-role="subject-code"]').textContent = subject.code || '—';
    // Always take the picked subject's credit (still editable afterwards).
    field(rowEl, 'credit').value = subject.credit || 3;
    rowEl.querySelector('[data-role="subject-options"]').classList.add('hidden');
    toggleEditSubject(rowEl);
    recalc(dom);
    field(rowEl, 'score').focus();
}

/** Typing filters; leaving the box without picking restores the chosen subject's label. */
function closeOptions(rowEl) {
    rowEl.querySelector('[data-role="subject-options"]')?.classList.add('hidden');
    const id = field(rowEl, 'subject_id').value;
    const subject = findSubject(id);
    if (subject) field(rowEl, 'subject_search').value = subjectLabel(subject);
    else if (!id) field(rowEl, 'subject_search').value = '';
}

function moveActive(rowEl, step) {
    const box = rowEl.querySelector('[data-role="subject-options"]');
    const options = [...box.querySelectorAll('.subject-option:not([disabled])')];
    if (!options.length) return;
    const index = (Number(box.dataset.active) + step + options.length) % options.length;
    box.dataset.active = String(index);
    options.forEach((o, i) => o.classList.toggle('bg-indigo-50', i === index));
    options[index].scrollIntoView({ block: 'nearest' });
}

/**
 * Fixes a wrong code/title in the master Subjects list from inside the modal.
 * SubjectRequest validates the whole record, so the current one is fetched
 * and only the edited fields are replaced. Saved transcripts keep their old
 * snapshot until they are saved again (syncSubjects re-reads the master).
 */
const SUBJECT_EDIT_FIELDS = ['code', 'name_en', 'name_kh', 'credit'];

async function openSubjectEdit(dom, ApiService, rowEl) {
    const id = field(rowEl, 'subject_id').value;
    const { error, data } = await ApiService.request(`${CONFIG.SUBJECTS_API}/${id}`);
    const record = data?.data ?? data;
    if (error || !record?.id) {
        Toast.fire({ icon: 'error', title: data?.message || 'Cannot load the subject' });
        return;
    }

    state.editingSubject = record;
    SUBJECT_EDIT_FIELDS.forEach((name) => (dom.subjectForm.elements[name].value = record[name] ?? ''));
    window.SubjectEditModal.toggle(true);
    setTimeout(() => dom.subjectForm.elements.code.focus(), 250);
}

async function saveSubjectEdit(dom, ApiService) {
    const record = state.editingSubject;
    if (!record) return;

    const values = {};
    SUBJECT_EDIT_FIELDS.forEach((name) => (values[name] = dom.subjectForm.elements[name].value.trim()));
    values.credit = values.credit === '' ? null : Number(values.credit);

    const { error, data } = await ApiService.request(`${CONFIG.SUBJECTS_API}/${record.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            faculty_id: record.faculty?.id ?? record.faculty_id ?? null,
            level: record.level,
            lecturer_hour: record.lecturer_hour ?? null,
            remark: record.remark ?? null,
            ...values,
        }),
    });
    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || 'មិនអាចរក្សាទុកបានទេ' });
        return;
    }

    const subject = findSubject(record.id);
    if (subject) Object.assign(subject, values);
    dom.form.querySelectorAll('.course-row').forEach((r) => {
        if (field(r, 'subject_id').value !== String(record.id)) return;
        field(r, 'subject_search').value = subjectLabel(subject ?? values);
        r.querySelector('[data-role="subject-code"]').textContent = values.code || '—';
    });

    state.editingSubject = null;
    window.SubjectEditModal.toggle(false);
    Toast.fire({ icon: 'success', title: 'Subject updated — Save the transcript to update its printout' });
}

export function bindSubjectEdit(dom, ApiService) {
    dom.subjectForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        saveSubjectEdit(dom, ApiService);
    });
}

/** All course-row interaction, delegated once on the form. */
export function bindCourses(dom, ApiService) {
    dom.form.addEventListener('click', (e) => {
        const tab = e.target.closest('[data-year-tab]');
        if (tab) return showYear(dom, tab.dataset.yearTab);

        const edit = e.target.closest('[data-action="edit-subject"]');
        if (edit) return openSubjectEdit(dom, ApiService, edit.closest('.course-row'));

        const add = e.target.closest('[data-action="add-course"]');
        if (add) {
            const rowEl = addCourseRow(dom, add.dataset.year, add.dataset.semester);
            if (rowEl) field(rowEl, 'subject_search').focus();
            return;
        }

        const remove = e.target.closest('[data-action="remove-course"]');
        if (remove) {
            remove.closest('.course-row')?.remove();
            recalc(dom);
        }
    });

    // mousedown, not click — fires before the search box's blur hides the list.
    dom.form.addEventListener('mousedown', (e) => {
        const option = e.target.closest('.subject-option');
        if (!option || option.disabled) return;
        e.preventDefault();
        pickSubject(dom, option.closest('.course-row'), findSubject(option.dataset.subjectId));
    });

    dom.form.addEventListener('focusin', (e) => {
        if (e.target.matches('[data-field="subject_search"]')) {
            e.target.select();
            renderOptions(dom, e.target.closest('.course-row'));
        }
    });

    dom.form.addEventListener('focusout', (e) => {
        if (e.target.matches('[data-field="subject_search"]')) closeOptions(e.target.closest('.course-row'));
    });

    dom.form.addEventListener('input', (e) => {
        if (e.target.matches('[data-field="subject_search"]')) renderOptions(dom, e.target.closest('.course-row'));
        else if (e.target.matches('[data-field="score"], [data-field="credit"]')) recalc(dom);
    });

    dom.form.addEventListener('keydown', (e) => {
        if (!e.target.matches('[data-field="subject_search"]')) return;
        const rowEl = e.target.closest('.course-row');
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            moveActive(rowEl, e.key === 'ArrowDown' ? 1 : -1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const box = rowEl.querySelector('[data-role="subject-options"]');
            const options = [...box.querySelectorAll('.subject-option:not([disabled])')];
            const chosen = options[Number(box.dataset.active)] ?? (options.length === 1 ? options[0] : null);
            if (chosen) pickSubject(dom, rowEl, findSubject(chosen.dataset.subjectId));
        } else if (e.key === 'Escape') {
            e.stopPropagation();
            e.target.blur();
        }
    });
}

/** English date → Khmer lunar (ចន្ទគតិ) date via momentkh; still editable by hand. */
export function bindKhmerDate(dom) {
    dom.form.elements.issue_date?.addEventListener('change', (e) => {
        const value = e.target.value;
        if (!value || typeof momentkh === 'undefined') return;
        const [year, month, day] = value.split('-').map(Number);
        dom.form.elements.date_kh.value = momentkh.format(momentkh.fromGregorian(year, month, day));
    });
}

/* ---------- Save / delete ---------- */

function collectPayload(dom) {
    const payload = {};
    FIELDS.forEach((name) => {
        const value = dom.form.elements[name]?.value.trim() ?? '';
        payload[name] = value === '' ? null : value;
    });
    ['name', 'surname'].forEach((k) => payload[k] && (payload[k] = payload[k].toUpperCase()));
    payload.entrance_exam = dom.form.elements.entrance_exam.checked;
    payload.comprehensive_exam = dom.form.elements.comprehensive_exam.checked;

    // Boxes past the degree's length are hidden and never count as paid.
    const years = programYears(dom);
    payload.payments = paymentBoxes(dom)
        .filter((box) => box.checked)
        .map((box) => box.value.split('-').map(Number))
        .filter(([year]) => year <= years)
        .map(([year, semester]) => ({ year, semester }));

    // Every doc type stores its courses and scores, even if its layout doesn't print them.
    payload.subjects = [];
    for (let year = 1; year <= CONFIG.YEARS; year++) {
        for (const semester of [1, 2]) {
            listFor(dom, year, semester).querySelectorAll('.course-row').forEach((rowEl) => {
                const score = field(rowEl, 'score').value.trim();
                payload.subjects.push({
                    subject_id: Number(field(rowEl, 'subject_id').value) || null,
                    year,
                    semester,
                    credit: field(rowEl, 'credit').value === '' ? null : Number(field(rowEl, 'credit').value),
                    score: score === '' ? null : Number(score),
                });
            });
        }
    }
    return payload;
}

/** Client-side check so the user lands on the right year tab instead of a vague server error. */
function findIncompleteRow(dom) {
    for (const rowEl of dom.form.querySelectorAll('.course-row')) {
        if (!field(rowEl, 'subject_id').value) return { rowEl, message: 'Please choose a subject for every row' };
        if (field(rowEl, 'credit').value === '') return { rowEl, message: 'Please enter the credit for every subject' };
    }
    return null;
}

/**
 * Same student ID already requested? Show when and by whom — creating is
 * still allowed. Skipped when an existing record keeps its own ID.
 * Resolves false when the user cancels.
 */
async function confirmRepeatStudent(dom, ApiService) {
    const code = dom.form.elements.student_code.value.trim();
    const editing = state.editingId;
    if (!code || (editing && getRenderedRow(editing)?.student_code === code)) return true;

    const params = new URLSearchParams({ student_code: code });
    if (editing) params.set('except', editing);
    const { error, data } = await ApiService.request(`${CONFIG.TRANSCRIPTS_API}/student-requests?${params}`);
    // A failed lookup shouldn't block saving.
    const earlier = error ? [] : (data?.data ?? data ?? []);
    if (!earlier.length) return true;

    const { isConfirmed } = await Swal.fire({
        html: repeatStudentHtml(code, earlier, Boolean(editing)),
        width: 500,
        showCancelButton: true,
        reverseButtons: true,
        buttonsStyling: false,
        focusCancel: true,
        confirmButtonText: 'បន្ត (Continue anyway)',
        cancelButtonText: 'បោះបង់ (Cancel)',
        customClass: {
            popup: '!rounded-2xl !p-0 overflow-hidden dark:!bg-neutral-900 border border-neutral-200 dark:border-white/10',
            htmlContainer: '!m-0 !p-0 !text-left',
            actions: '!w-full !mt-0 !mb-0 !px-6 !py-4 !justify-end gap-2 border-t border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5',
            confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900',
            cancelButton: 'px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/10 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
        },
    });
    return isConfirmed;
}

const pill = (tone, text) => `<span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold ${tone}">${text}</span>`;

/** Header + one card per earlier request, styled like the print picker. */
function repeatStudentHtml(code, earlier, editing) {
    const nameKh = earlier.find((r) => r.name_kh)?.name_kh;

    const cards = earlier.map((r) => `
        <li class="p-3.5 rounded-xl border border-neutral-200 dark:border-white/10 bg-white dark:bg-white/[0.02]">
            <div class="flex items-center gap-2">
                <span class="font-mono text-sm font-bold text-neutral-900 dark:text-white truncate">${escapeHtml(r.issue_no || '—')}</span>
                ${r.is_draft ? pill('bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400', 'Draft') : ''}
                <span class="ms-auto"></span>
                ${r.prints
                    ? pill('bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400', `បោះពុម្ព ${r.prints}×`)
                    : pill('bg-neutral-100 text-neutral-500 dark:bg-white/5 dark:text-neutral-400', 'មិនទាន់បោះពុម្ព')}
            </div>
            <div class="mt-1 text-xs font-medium text-indigo-600 dark:text-indigo-400">${escapeHtml(r.request_type ?? '')}</div>
            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-neutral-500 dark:text-neutral-400">
                <span>🕒 ${escapeHtml(r.requested_at ?? '—')}</span>
                <span>👤 ${escapeHtml(r.requested_by ?? '—')}</span>
            </div>
        </li>`).join('');

    return `
        <div class="px-6 pt-6 pb-5">
            <div class="flex items-start gap-3">
                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-full bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </span>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-neutral-900 dark:text-white">និស្សិតនេះបានស្នើរួចហើយ</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">This student ID already has ${earlier.length} request${earlier.length > 1 ? 's' : ''}</p>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-neutral-100 dark:bg-white/5">
                <span class="font-mono text-sm font-bold text-neutral-900 dark:text-white">${escapeHtml(code)}</span>
                ${nameKh ? `<span class="text-sm text-neutral-600 dark:text-neutral-300 truncate">${escapeHtml(nameKh)}</span>` : ''}
            </div>

            <ul class="mt-3 space-y-2 max-h-64 overflow-y-auto">${cards}</ul>

            <p class="mt-4 text-xs text-neutral-500 dark:text-neutral-400">
                You can still continue. ${editing ? 'Cancel puts the saved ID back.' : 'Cancel closes this form without saving.'}
            </p>
        </div>`;
}

/**
 * Checked as soon as the ID is entered (change = on leaving the field), not
 * at Save, so nobody fills the whole form first. Cancel drops the request:
 * a new one closes the form, an edit gets its saved ID back.
 */
export function bindStudentCode(dom, ApiService) {
    dom.form.elements.student_code?.addEventListener('change', async (e) => {
        if (await confirmRepeatStudent(dom, ApiService)) return;

        if (state.editingId) {
            e.target.value = getRenderedRow(state.editingId)?.student_code ?? '';
        } else {
            window.TranscriptModal.toggle(false);
        }
    });
}

export async function submitForm(dom, ApiService, onDone, { draft = false } = {}) {
    const problem = draft ? null : findIncompleteRow(dom);
    if (problem) {
        showYear(dom, problem.rowEl.closest('[data-year-panel]').dataset.yearPanel);
        field(problem.rowEl, 'subject_id').value ? field(problem.rowEl, 'credit').focus() : field(problem.rowEl, 'subject_search').focus();
        Toast.fire({ icon: 'warning', title: problem.message });
        return;
    }

    const editing = state.editingId;
    const url = editing ? `${CONFIG.TRANSCRIPTS_API}/${editing}` : CONFIG.TRANSCRIPTS_API;
    const payload = { ...collectPayload(dom), draft };

    const { error, data } = await ApiService.request(url, {
        method: editing ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });

    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || 'មិនអាចរក្សាទុកបានទេ' });
        return;
    }

    window.TranscriptModal.toggle(false);
    state.editingId = null;
    onDone();

    const blockers = (data?.data ?? data)?.print_blockers ?? [];
    if (draft) {
        Toast.fire({ icon: 'success', title: 'រក្សាទុកព្រាងហើយ (Draft saved) — open it with Edit to continue' });
    } else if (!blockers.length) {
        Toast.fire({ icon: 'success', title: 'រក្សាទុកជោគជ័យ!' });
    } else {
        Swal.fire({
            icon: 'info',
            title: 'Saved — cannot print yet',
            html: `<ul class="text-left list-disc ps-6">${blockers.map((b) => `<li>${escapeHtml(b)}</li>`).join('')}</ul>`,
        });
    }
}

export async function handleDelete(ApiService, id, onDone) {
    const row = getRenderedRow(id);
    const confirmation = await Swal.fire({
        title: 'តើអ្នកប្រាកដជាចង់លុបមែនទេ?',
        text: `${row?.issue_no ?? ''} — ${row?.surname ?? ''} ${row?.name ?? ''} (Moved to trash)`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'បាទ/ចាស លុបវា!',
        cancelButtonText: 'បោះបង់',
    });
    if (!confirmation.isConfirmed) return;

    const { error, data } = await ApiService.request(`${CONFIG.TRANSCRIPTS_API}/${id}`, { method: 'DELETE' });
    if (error) {
        Toast.fire({ icon: 'error', title: data?.message || 'មិនអាចលុបបានទេ' });
        return;
    }
    Toast.fire({ icon: 'success', title: 'លុបទិន្នន័យបានជោគជ័យ!' });
    onDone();
}
