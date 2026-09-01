/**
 * Manages the "Attendance per Session" block in the exam-room admin modal —
 * a manual override so a super admin can fix a wrong absent count without
 * going through the public per-room attendance page.
 *
 * The `absences` array is positional (index 0/1/2 = session 1/2/3), same
 * shape the public attendance page writes: [{ major, time, total }, ...].
 * A session left blank stores total: null, meaning "not recorded yet" —
 * distinct from an explicit 0 (everyone present) — matching how the public
 * page and the admin table's attendance pills both read this field.
 */

const SESSION_LABELS = ['វគ្គទី១ (Session 1)', 'វគ្គទី២ (Session 2)', 'វគ្គទី៣ (Session 3)'];
const ROUND_TIMES = ['08:00 AM', '10:00 AM', '01:00 PM'];

function buildRows(dom) {
    const container = dom.form.querySelector('#absencesContainer');
    if (!container) return;

    container.innerHTML = SESSION_LABELS.map((label, i) => `
        <div>
            <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-1">${label}</label>
            <input type="number" min="0" placeholder="—" data-session="${i}"
                   class="absence-session-input w-full px-3 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40">
        </div>`).join('');
}

/** Blanks all 3 session inputs — used when opening a fresh "create" form. */
export function resetAbsenceInputs(dom) {
    buildRows(dom);
}

/** Fills the 3 session inputs from an existing room's absences array — used when opening "edit". */
export function loadAbsenceInputs(dom, absences) {
    buildRows(dom);
    const container = dom.form.querySelector('#absencesContainer');
    if (!container || !Array.isArray(absences)) return;

    absences.forEach((session, i) => {
        const input = container.querySelector(`[data-session="${i}"]`);
        const total = session?.total;
        if (input && total !== null && total !== undefined && total !== '') {
            input.value = total;
        }
    });
}

/**
 * Reads the 3 inputs back out as a positional [{major, time, total}, ...]
 * array. `major` defaults to the room's overall major so each session entry
 * stays self-describing even if the admin never touched the majors rows.
 */
export function collectAbsences(dom, fallbackMajor = '') {
    const container = dom.form.querySelector('#absencesContainer');
    if (!container) return [];

    return [0, 1, 2].map((i) => {
        const input = container.querySelector(`[data-session="${i}"]`);
        const raw = input?.value ?? '';
        return {
            major: fallbackMajor,
            time: ROUND_TIMES[i],
            total: raw === '' ? null : Number(raw),
        };
    });
}
