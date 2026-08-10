/**
 * Manages the clonable "Majors Breakdown" rows inside the exam-room form.
 * Each row is [major name, time slot, total]. The form's student_total field
 * is read-only and always recomputed as the sum of these rows.
 */

const TIME_SLOTS = ['7:30-9:00', '9:15-10:48', '10:45-12:15'];

function recalcStudentTotal(dom) {
    const totals = dom.form.querySelectorAll('.major-total-input');
    const sum = Array.from(totals).reduce((acc, input) => acc + (Number(input.value) || 0), 0);
    const totalField = dom.form.querySelector('[name="student_total"]');
    if (totalField) totalField.value = sum;
}

function timeOptionsHtml(selected) {
    return TIME_SLOTS.map((slot) =>
        `<option value="${slot}" ${slot === selected ? 'selected' : ''}>${slot}</option>`
    ).join('');
}

export function addMajorRow(dom, major = '', total = '', time = '') {
    const container = dom.form.querySelector('#majorsContainer');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'major-row flex flex-wrap items-center gap-2';
    row.innerHTML = `
        <input type="text" placeholder="ជំនាញ (e.g. LAW)" value="${major}"
               class="major-name-input flex-1 min-w-0 basis-24 px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
        <select class="major-time-input w-28 shrink-0 px-2 py-2 text-xs sm:text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
            ${timeOptionsHtml(time || TIME_SLOTS[0])}
        </select>
        <input type="number" min="0" placeholder="ចំនួន" value="${total}"
               class="major-total-input w-16 shrink-0 px-2 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
        <button type="button" class="remove-major-row p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;

    container.appendChild(row);
    recalcStudentTotal(dom);
}

/** Clears all rows and adds back a single blank one — used when opening a fresh "create" form. */
export function resetMajorsRows(dom) {
    const container = dom.form.querySelector('#majorsContainer');
    if (container) container.innerHTML = '';
    addMajorRow(dom);
}

/** Clears all rows and repopulates from an existing room's majors array — used when opening "edit". */
export function loadMajorsRows(dom, majors) {
    const container = dom.form.querySelector('#majorsContainer');
    if (container) container.innerHTML = '';

    if (Array.isArray(majors) && majors.length > 0) {
        majors.forEach((m) => addMajorRow(dom, m.major ?? '', m.total ?? '', m.time ?? ''));
    } else {
        addMajorRow(dom);
    }
}

/** Reads the current rows back out as [{major, time, total}, ...], dropping blank rows. */
export function collectMajorsRows(dom) {
    const rows = dom.form.querySelectorAll('.major-row');
    return Array.from(rows)
        .map((row) => ({
            major: row.querySelector('.major-name-input')?.value.trim() ?? '',
            time: row.querySelector('.major-time-input')?.value ?? '',
            total: Number(row.querySelector('.major-total-input')?.value) || 0,
        }))
        .filter((m) => m.major);
}

export function bindMajorsEvents(dom) {
    dom.form?.querySelector('#addMajorRowBtn')?.addEventListener('click', () => addMajorRow(dom));

    dom.form?.addEventListener('click', (e) => {
        if (e.target.closest('.remove-major-row')) {
            e.target.closest('.major-row')?.remove();
            recalcStudentTotal(dom);
        }
    });

    dom.form?.addEventListener('input', (e) => {
        if (e.target.classList.contains('major-total-input')) {
            recalcStudentTotal(dom);
        }
    });
}
