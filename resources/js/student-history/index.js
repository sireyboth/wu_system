/**
 * Student History page — search for a student, then show their full
 * student_academic_histories timeline (newest first), each row already
 * a frozen snapshot of batch/major/shift/group/campus/status/year_level/
 * semester as of that point, tagged with whichever term was active then.
 */
import { getById, baseUri } from '../app.js';

const DOM = {
    searchInput: getById('historySearchInput'),
    searchResults: getById('historySearchResults'),
    emptyState: getById('historyEmptyState'),
    content: getById('historyContent'),
    studentInitial: getById('historyStudentInitial'),
    studentName: getById('historyStudentName'),
    studentCode: getById('historyStudentCode'),
    timeline: getById('historyTimeline'),
};

let debounceTimer = null;

async function apiGet(url) {
    const res = await fetch(url, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
    });
    if (!res.ok) return { error: true, data: null };
    return { error: false, data: await res.json() };
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

async function runSearch(query) {
    if (!query.trim()) {
        DOM.searchResults.classList.add('hidden');
        return;
    }

    const { error, data } = await apiGet(`${baseUri('students')}?search=${encodeURIComponent(query)}&per_page=8`);
    if (error) return;

    const students = data?.data ?? data ?? [];
    if (students.length === 0) {
        DOM.searchResults.innerHTML = '<div class="px-4 py-3 text-sm text-neutral-400">No students found.</div>';
        DOM.searchResults.classList.remove('hidden');
        return;
    }

    DOM.searchResults.innerHTML = students.map((student) => {
        const person = student.person ?? {};
        const name = `${person.first_name ?? ''} ${person.last_name ?? ''}`.trim() || 'Unnamed';
        return `
            <button type="button" data-id="${student.id}" class="history-result-item w-full text-left px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors">
                <div class="font-semibold text-sm text-neutral-900 dark:text-white">${escapeHtml(name)}</div>
                <div class="text-xs text-neutral-400 font-mono">${escapeHtml(student.code ?? '')}</div>
            </button>`;
    }).join('');
    DOM.searchResults.classList.remove('hidden');
}

function fieldLabel(record, key, fallback = '—') {
    return record?.[key]?.name_kh || record?.[key]?.name || record?.[key]?.name_en || fallback;
}

function renderTimeline(history) {
    if (!history || history.length === 0) {
        DOM.timeline.innerHTML = '<p class="text-sm text-neutral-400 py-6">No academic history recorded for this student yet.</p>';
        return;
    }

    // Backend returns oldest-first; show most recent at the top.
    const ordered = [...history].reverse();

    DOM.timeline.innerHTML = `
        <div class="absolute left-[7px] top-2 bottom-2 w-px bg-neutral-200 dark:bg-white/10"></div>
        ${ordered.map((row) => {
            const dotClasses = row.is_current
                ? 'bg-emerald-500 ring-4 ring-emerald-500/20'
                : 'bg-neutral-300 dark:bg-neutral-600';
            const dateLabel = row.effective_date
                ? new Date(row.effective_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                : 'Unknown date';
            const termLabel = row.term ? (row.term.code ?? row.term.full_name ?? '') : 'No term recorded';
            const semesterLabel = row.semester ? `Semester ${row.semester}` : 'Semester not set';

            return `
            <div class="relative">
                <span class="absolute -left-8 top-1.5 w-3.5 h-3.5 rounded-full ${dotClasses}"></span>
                <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm p-5">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-xs font-mono text-neutral-400">${dateLabel}</span>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">${escapeHtml(termLabel)}</span>
                        ${row.is_current ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Current</span>' : ''}
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">Year / Semester</div>
                            <div class="font-semibold text-neutral-800 dark:text-neutral-200">Year ${row.year_level ?? '—'} · ${semesterLabel}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">Batch</div>
                            <div class="font-semibold text-neutral-800 dark:text-neutral-200">${escapeHtml(fieldLabel(row, 'batch'))}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">Major</div>
                            <div class="font-semibold text-neutral-800 dark:text-neutral-200">${escapeHtml(fieldLabel(row, 'major'))}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">Status</div>
                            <div class="font-semibold text-neutral-800 dark:text-neutral-200">${escapeHtml(fieldLabel(row, 'status'))}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">Shift</div>
                            <div class="text-neutral-600 dark:text-neutral-400">${escapeHtml(fieldLabel(row, 'shift'))}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">Group</div>
                            <div class="text-neutral-600 dark:text-neutral-400">${escapeHtml(fieldLabel(row, 'group'))}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">Campus</div>
                            <div class="text-neutral-600 dark:text-neutral-400">${escapeHtml(fieldLabel(row, 'campus'))}</div>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('')}
    `;
}

async function selectStudent(studentId) {
    DOM.searchResults.classList.add('hidden');

    const { error: studentError, data: studentData } = await apiGet(`${baseUri('students')}/${studentId}`);
    const { error: historyError, data: historyData } = await apiGet(`${baseUri('students')}/${studentId}/academic-history`);

    if (studentError) return;

    const student = studentData?.data ?? studentData;
    const person = student.person ?? {};
    const name = `${person.first_name ?? ''} ${person.last_name ?? ''}`.trim() || 'Unnamed';

    DOM.studentInitial.textContent = name.charAt(0).toUpperCase() || '?';
    DOM.studentName.textContent = name;
    DOM.studentCode.textContent = student.code ?? '—';

    renderTimeline(historyError ? [] : (historyData?.data ?? historyData ?? []));

    DOM.emptyState.classList.add('hidden');
    DOM.content.classList.remove('hidden');
    DOM.searchInput.value = `${name} (${student.code ?? ''})`;
}

document.addEventListener('DOMContentLoaded', () => {
    if (!DOM.searchInput) return;

    DOM.searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => runSearch(e.target.value), 300);
    });

    DOM.searchInput.addEventListener('focus', () => {
        if (DOM.searchResults.innerHTML.trim()) DOM.searchResults.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        const item = e.target.closest('.history-result-item');
        if (item) {
            selectStudent(item.getAttribute('data-id'));
            return;
        }
        if (!e.target.closest('#historySearchInput') && !e.target.closest('#historySearchResults')) {
            DOM.searchResults?.classList.add('hidden');
        }
    });
});
