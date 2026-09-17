/**
 * Lecturer's own portal — only classes the logged-in lecturer is assigned
 * to (enforced server-side in LecturerPortalController, not just hidden
 * here). This is where score config and score entry actually belong, per
 * the correction that a registrar shouldn't be setting a lecturer's own
 * point split on their behalf.
 */
import QRCode from 'qrcode';

(() => {
    'use strict';

    const CONFIG = {
        API_CLASSES: '/api/v1/lecturer-portal/classes',
        API_SCORE_CONFIG: (classId) => `/api/v1/lecturer-portal/classes/${classId}/score-config`,
        API_ROSTER: (classId) => `/api/v1/lecturer-portal/classes/${classId}/roster`,
        API_SCORES: '/api/v1/lecturer-portal/scores',
        API_START_SESSION: (classId) => `/api/v1/lecturer-portal/classes/${classId}/sessions`,
        API_SESSION: (sessionId) => `/api/v1/lecturer-portal/sessions/${sessionId}`,
        API_QR_TOKEN: (sessionId) => `/api/v1/lecturer-portal/sessions/${sessionId}/qr-token`,
        API_MARK: (sessionId) => `/api/v1/lecturer-portal/sessions/${sessionId}/mark`,
        API_SUBMIT_SESSION: (sessionId) => `/api/v1/lecturer-portal/sessions/${sessionId}/submit`,
        API_ATTENDANCE_HISTORY: (classId) => `/api/v1/lecturer-portal/classes/${classId}/attendance-history`,
        API_ATTENDANCE_HISTORY_EXPORT: (classId) => `/api/v1/lecturer-portal/classes/${classId}/attendance-history/export`,
        API_ATTENDANCE_HISTORY_IMPORT: (classId) => `/api/v1/lecturer-portal/classes/${classId}/attendance-history/import`,
    };

    const Toast = typeof Swal !== 'undefined' ? Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    }) : { fire: console.log };

    const DOM = {
        loader: document.getElementById('loading-overlay'),
        tableBody: document.getElementById('my-class-table-body'),

        scoreConfigForm: document.getElementById('scoreConfigForm'),
        scoreConfigModal: document.getElementById('scoreConfigModal'),
        scoreConfigModalCard: document.getElementById('scoreConfigModalCard'),
        scoreConfigClassCode: document.getElementById('scoreConfigClassCode'),
        scoreConfigClassId: document.getElementById('scoreConfigClassId'),
        scoreConfigTotalBanner: document.getElementById('scoreConfigTotalBanner'),
        scoreConfigTotalValue: document.getElementById('scoreConfigTotalValue'),

        rosterModal: document.getElementById('rosterModal'),
        rosterModalCard: document.getElementById('rosterModalCard'),
        rosterClassCode: document.getElementById('rosterClassCode'),
        rosterTableBody: document.getElementById('rosterTableBody'),
        rosterSearchInput: document.getElementById('rosterSearchInput'),
        rosterHistoryBtn: document.getElementById('rosterHistoryBtn'),

        attendanceHistoryModal: document.getElementById('attendanceHistoryModal'),
        attendanceHistoryModalCard: document.getElementById('attendanceHistoryModalCard'),
        attendanceHistoryClassCode: document.getElementById('attendanceHistoryClassCode'),
        attendanceHistoryHead: document.getElementById('attendanceHistoryHead'),
        attendanceHistoryBody: document.getElementById('attendanceHistoryBody'),
        attendanceHistorySessionCount: document.getElementById('attendanceHistorySessionCount'),
        attendanceHistoryExportBtn: document.getElementById('attendanceHistoryExportBtn'),
        attendanceHistoryImportBtn: document.getElementById('attendanceHistoryImportBtn'),
        attendanceHistoryImportFile: document.getElementById('attendanceHistoryImportFile'),

        attendanceModal: document.getElementById('attendanceModal'),
        attendanceModalCard: document.getElementById('attendanceModalCard'),
        attendanceClassCode: document.getElementById('attendanceClassCode'),
        attendanceNoSession: document.getElementById('attendanceNoSession'),
        attendanceLive: document.getElementById('attendanceLive'),
        attendanceStartBtn: document.getElementById('attendanceStartBtn'),
        attendanceSubmitBtn: document.getElementById('attendanceSubmitBtn'),
        attendanceStartNextBtn: document.getElementById('attendanceStartNextBtn'),
        attendanceQrImg: document.getElementById('attendanceQrImg'),
        attendanceTestLink: document.getElementById('attendanceTestLink'),
        attendanceSessionStatus: document.getElementById('attendanceSessionStatus'),
        attendanceStatusDot: document.getElementById('attendanceStatusDot'),
        attendanceCount: document.getElementById('attendanceCount'),
        attendanceRosterList: document.getElementById('attendanceRosterList'),
        attendanceClockTime: document.getElementById('attendanceClockTime'),
        attendanceClockDate: document.getElementById('attendanceClockDate'),
    };

    const rosterState = { enrollments: [], classId: null };
    const attendanceState = { classId: null, sessionId: null, qrTimer: null, pollTimer: null, clockTimer: null };
    const historyState = { classId: null };

    const ApiService = {
        async request(url, options = {}) {
            toggleLoader(true);
            try {
                const { headers, method = 'GET', body, ...rest } = options;
                const response = await fetch(url, {
                    method, credentials: 'same-origin',
                    headers: { Accept: 'application/json', ...headers },
                    body, ...rest,
                });
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                const result = isJson ? await response.json() : null;
                return { error: !response.ok, status: response.status, data: result };
            } catch (err) {
                console.error(`[API Error] ${url}:`, err);
                return { error: true, status: 500, data: null };
            } finally {
                toggleLoader(false);
            }
        },
    };

    function toggleLoader(show) {
        DOM.loader?.classList.toggle('hidden', !show);
    }

    function toggleModalEl(modalEl, cardEl, forceOpen = null) {
        if (!modalEl || !cardEl) return;
        const isOpen = modalEl.classList.contains('flex');
        const makeOpen = forceOpen !== null ? forceOpen : !isOpen;

        if (makeOpen) {
            modalEl.classList.remove('invisible');
            modalEl.classList.add('flex');
            requestAnimationFrame(() => {
                modalEl.classList.remove('opacity-0');
                cardEl.classList.remove('scale-90', 'opacity-0');
                cardEl.classList.add('scale-100', 'opacity-100');
            });
        } else {
            modalEl.classList.add('opacity-0');
            cardEl.classList.remove('scale-100', 'opacity-100');
            cardEl.classList.add('scale-90', 'opacity-0');
            setTimeout(() => {
                modalEl.classList.add('invisible');
                modalEl.classList.remove('flex');
            }, 300);
        }
    }

    // ---- My classes list ----

    async function loadClasses() {
        const { error, data } = await ApiService.request(`${CONFIG.API_CLASSES}?per_page=100`);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load your classes.' });
            return;
        }
        renderTable(data?.data ?? []);
    }

    function scoreBadge(config) {
        if (!config || !config.id) {
            return '<span class="text-xs italic text-neutral-400">Not set — click Score Config</span>';
        }
        const total = config.total_max ?? 0;
        const color = total > 100 ? 'text-rose-600' : total === 100 ? 'text-emerald-600' : 'text-amber-600';
        return `<span class="text-xs font-bold ${color}">${total} / 100</span>`;
    }

    function renderTable(classes) {
        if (!DOM.tableBody) return;
        if (!classes.length) {
            DOM.tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-neutral-500">No classes assigned to you yet — ask a registrar to assign you to one.</td></tr>';
            return;
        }

        DOM.tableBody.innerHTML = classes.map((cls, i) => `
            <tr class="hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 border-b border-neutral-100 dark:border-white/5">
                <td class="px-6 py-4 text-neutral-500 font-mono text-sm">${i + 1}</td>
                <td class="px-6 py-4 font-mono font-bold text-indigo-600">${cls.code}</td>
                <td class="px-6 py-4 text-sm">${cls.subject?.code ?? ''} <span class="text-neutral-400">${cls.subject?.name_en ?? ''}</span></td>
                <td class="px-6 py-4 text-sm">${cls.term?.code ?? ''}</td>
                <td class="px-6 py-4 text-center text-sm font-bold">${cls.enrolled_count ?? 0}</td>
                <td class="px-6 py-4">${scoreBadge(cls.score_config)}</td>
                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                    <button data-action="attendance" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 rounded-lg">Attendance</button>
                    <button data-action="score-config" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg">Score Config</button>
                    <button data-action="roster" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 dark:hover:bg-white/10 rounded-lg">Roster</button>
                </td>
            </tr>`).join('');
    }

    // ---- Score config (mine only) ----

    function recalcScoreTotal() {
        const inputs = DOM.scoreConfigForm.querySelectorAll('.score-max-input');
        let total = 0;
        inputs.forEach((input) => { total += Number(input.value) || 0; });

        DOM.scoreConfigTotalValue.textContent = `${total} / 100`;
        const overCap = total > 100;
        DOM.scoreConfigTotalBanner.classList.toggle('border-rose-300', overCap);
        DOM.scoreConfigTotalBanner.classList.toggle('bg-rose-50', overCap);
        DOM.scoreConfigTotalBanner.classList.toggle('text-rose-700', overCap);
        DOM.scoreConfigTotalBanner.classList.toggle('border-emerald-300', !overCap);
        DOM.scoreConfigTotalBanner.classList.toggle('bg-emerald-50', !overCap);
        DOM.scoreConfigTotalBanner.classList.toggle('text-emerald-700', !overCap);
    }

    async function openScoreConfig(classId, code) {
        DOM.scoreConfigClassId.value = classId;
        DOM.scoreConfigClassCode.textContent = code;

        const { error, data } = await ApiService.request(CONFIG.API_SCORE_CONFIG(classId));
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || "You aren't assigned to this class." });
            return;
        }
        const config = data?.data ?? {};

        ['homework_max', 'quiz_max', 'assignment_max', 'midterm_max', 'final_max', 'attendance_max'].forEach((field) => {
            DOM.scoreConfigForm.querySelector(`[name="${field}"]`).value = config[field] ?? 0;
        });
        DOM.scoreConfigForm.querySelector('[name="total_weeks"]').value = config.total_weeks ?? 15;
        DOM.scoreConfigForm.querySelector('[name="sessions_per_week"]').value = config.sessions_per_week ?? 2;
        recalcScoreTotal();
        toggleModalEl(DOM.scoreConfigModal, DOM.scoreConfigModalCard, true);
    }

    async function handleScoreConfigSubmit(e) {
        e.preventDefault();
        const classId = DOM.scoreConfigClassId.value;
        const payload = {};
        ['homework_max', 'quiz_max', 'assignment_max', 'midterm_max', 'final_max', 'attendance_max', 'total_weeks', 'sessions_per_week'].forEach((field) => {
            payload[field] = Number(DOM.scoreConfigForm.querySelector(`[name="${field}"]`).value) || 0;
        });

        const { error, data } = await ApiService.request(CONFIG.API_SCORE_CONFIG(classId), {
            method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload),
        });

        if (error) {
            const messages = data?.errors ? Object.values(data.errors).flat() : [data?.message || 'Failed to save config'];
            Toast.fire({ icon: 'error', title: messages[0] });
            return;
        }

        Toast.fire({ icon: 'success', title: 'Your score config was saved.' });
        toggleModalEl(DOM.scoreConfigModal, DOM.scoreConfigModalCard, false);
        loadClasses();
    }

    // ---- Roster / score entry (mine only) ----

    function studentLabel(enrollment) {
        const person = enrollment.student?.person;
        const nameKh = [person?.first_name_kh, person?.last_name_kh].filter(Boolean).join(' ');
        const nameEn = [person?.first_name, person?.last_name].filter(Boolean).join(' ');
        const name = nameKh || nameEn || '—';
        return `${enrollment.student?.code ?? ''} — ${name}`;
    }

    const SCORE_COMPONENTS = ['homework', 'quiz', 'assignment', 'midterm', 'final'];

    function scoreCell(enrollmentId, component, enr) {
        const existing = (enr.scores ?? []).find((s) => s.component === component);
        const value = existing ? existing.points : '';
        return `<td class="py-2 px-1 text-center">
            <input type="number" min="0" step="0.5" value="${value}"
                data-enrollment-id="${enrollmentId}" data-component="${component}" data-original="${value}"
                class="score-cell w-16 text-xs text-center px-2 py-1.5 bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
        </td>`;
    }

    function rowTotal(row) {
        let total = Number(row.dataset.attendanceScore) || 0;
        row.querySelectorAll('.score-cell').forEach((input) => { total += Number(input.value) || 0; });
        return total;
    }

    // Total Point is a live sum of whatever's currently in the five score
    // cells (saved or not) plus the auto-computed attendance score — Grade
    // Point is a placeholder column until the grading scale/formula is
    // decided, so it stays a dash for now.
    function renderRosterRow(enr) {
        const attendanceDisplay = enr.attendance_score === null || enr.attendance_score === undefined
            ? '<span class="text-xs italic text-neutral-400" title="Set your Score Config\'s Total Weeks / Sessions per Week to compute this">—</span>'
            : enr.attendance_score;
        return `
            <tr data-enrollment-id="${enr.id}" data-attendance-score="${enr.attendance_score ?? 0}">
                <td class="py-3 pr-4 font-medium">${studentLabel(enr)}</td>
                <td class="py-3 pr-4 text-xs uppercase text-neutral-500">${enr.status}</td>
                ${SCORE_COMPONENTS.map((c) => scoreCell(enr.id, c, enr)).join('')}
                <td class="py-3 pr-2 text-center font-semibold text-indigo-600 dark:text-indigo-400">${attendanceDisplay}</td>
                <td class="py-3 pr-3 text-center font-bold total-point-cell">${rowTotalFromEnrollment(enr)}</td>
                <td class="py-3 text-center text-neutral-400 grade-point-cell">—</td>
            </tr>`;
    }

    function rowTotalFromEnrollment(enr) {
        const typed = (enr.scores ?? []).reduce((sum, s) => sum + Number(s.points ?? 0), 0);
        return typed + Number(enr.attendance_score ?? 0);
    }

    function renderRosterRows(enrollments) {
        if (!enrollments.length) {
            DOM.rosterTableBody.innerHTML = '<tr><td colspan="10" class="py-6 text-center text-neutral-400">No students match your search.</td></tr>';
            return;
        }
        DOM.rosterTableBody.innerHTML = enrollments.map(renderRosterRow).join('');
    }

    async function openRoster(classId, code) {
        rosterState.classId = classId;
        DOM.rosterClassCode.textContent = code;
        if (DOM.rosterSearchInput) DOM.rosterSearchInput.value = '';
        DOM.rosterTableBody.innerHTML = '<tr><td colspan="10" class="py-6 text-center text-neutral-400">Loading roster...</td></tr>';
        toggleModalEl(DOM.rosterModal, DOM.rosterModalCard, true);

        const { error, data } = await ApiService.request(`${CONFIG.API_ROSTER(classId)}?per_page=200`);
        if (error) {
            DOM.rosterTableBody.innerHTML = `<tr><td colspan="10" class="py-6 text-center text-rose-500">${data?.message || 'Failed to load roster.'}</td></tr>`;
            return;
        }

        rosterState.enrollments = data?.data ?? [];
        if (!rosterState.enrollments.length) {
            DOM.rosterTableBody.innerHTML = '<tr><td colspan="10" class="py-6 text-center text-neutral-400">No students enrolled yet.</td></tr>';
            return;
        }

        renderRosterRows(rosterState.enrollments);
    }

    function filterRoster(keyword) {
        const term = keyword.trim().toLowerCase();
        if (!term) {
            renderRosterRows(rosterState.enrollments);
            return;
        }
        renderRosterRows(rosterState.enrollments.filter((enr) => studentLabel(enr).toLowerCase().includes(term)));
    }

    // ---- Attendance history (students x session dates grid) ----

    function historyStatusBadge(status) {
        const map = { present: 'bg-emerald-500', absent: 'bg-rose-500', late: 'bg-amber-500', excused: 'bg-sky-500' };
        const color = map[status] ?? 'bg-neutral-200 dark:bg-white/10';
        const letter = status ? status.charAt(0).toUpperCase() : '';
        return `<span class="inline-flex items-center justify-center w-6 h-6 rounded text-[10px] font-bold text-white ${color}" title="${status ?? 'No record'}">${letter}</span>`;
    }

    const HISTORY_STATUS_OPTIONS = ['present', 'late', 'absent', 'excused'];

    // Editable version of the badge above — a plain <select> styled to look
    // like the badge until opened, colored the same way. Changing it fires
    // straight through the ordinary mark-attendance endpoint, same one the
    // live QR modal's own dropdown uses, no correction/approval step, on
    // ANY past date (that endpoint never actually checks lock status —
    // only the live modal's own UI hides the dropdown once locked).
    function historyStatusEditable(sessionId, cell) {
        if (!cell) return '<span class="text-xs text-neutral-300">—</span>';
        const { status, session_roster_id: rosterId } = cell;
        const map = { present: 'bg-emerald-500', absent: 'bg-rose-500', late: 'bg-amber-500', excused: 'bg-sky-500' };
        const color = map[status] ?? 'bg-neutral-200 dark:bg-white/10 text-neutral-500';
        const options = ['', ...HISTORY_STATUS_OPTIONS].map((opt) => `<option value="${opt}" ${opt === (status ?? '') ? 'selected' : ''}>${opt ? opt.charAt(0).toUpperCase() + opt.slice(1) : '—'}</option>`).join('');
        return `<select data-action="history-mark" data-session-id="${sessionId}" data-session-roster-id="${rosterId}"
            class="w-9 h-6 text-[10px] font-bold text-center rounded border-0 outline-none cursor-pointer ${color} ${status ? 'text-white' : ''}">${options}</select>`;
    }

    function formatHistoryDate(dateStr) {
        return new Date(`${dateStr}T00:00:00`).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
    }

    function renderAttendanceHistory(history) {
        const sessions = history.sessions ?? [];
        const students = history.students ?? [];

        DOM.attendanceHistorySessionCount.textContent = `${sessions.length} session${sessions.length === 1 ? '' : 's'} held`;

        if (!sessions.length) {
            DOM.attendanceHistoryHead.innerHTML = '';
            DOM.attendanceHistoryBody.innerHTML = '<tr><td class="py-6 px-6 text-center text-neutral-400">No attendance session has been held for this class yet.</td></tr>';
            return;
        }

        DOM.attendanceHistoryHead.innerHTML = `
            <tr>
                <th class="py-2 pl-6 pr-3 sticky left-0 bg-white dark:bg-neutral-900">Student</th>
                ${sessions.map((s) => `<th class="py-2 px-2 text-center whitespace-nowrap">${formatHistoryDate(s.date)}${s.session_number > 1 ? ` · S${s.session_number}` : ''}${s.status !== 'locked' ? `<span class="block normal-case font-normal text-amber-500">${s.status}</span>` : ''}</th>`).join('')}
            </tr>`;

        if (!students.length) {
            DOM.attendanceHistoryBody.innerHTML = `<tr><td colspan="${sessions.length + 1}" class="py-6 px-6 text-center text-neutral-400">No students enrolled.</td></tr>`;
            return;
        }

        DOM.attendanceHistoryBody.innerHTML = students.map((student) => `
            <tr>
                <td class="py-2 pl-6 pr-3 font-medium whitespace-nowrap sticky left-0 bg-white dark:bg-neutral-900">${student.code ?? ''} — ${student.name}</td>
                ${sessions.map((s) => `<td class="py-2 px-2 text-center">${historyStatusEditable(s.id, student.statuses?.[s.id])}</td>`).join('')}
            </tr>`).join('');
    }

    async function markFromHistory(select) {
        const sessionId = select.dataset.sessionId;
        const sessionRosterId = select.dataset.sessionRosterId;
        const status = select.value;
        if (!status) return;

        const { error, data } = await ApiService.request(CONFIG.API_MARK(sessionId), {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ session_roster_id: sessionRosterId, status }),
        });

        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to update.' });
            return;
        }
        Toast.fire({ icon: 'success', title: `Marked ${status}.` });

        // Re-color this one cell in place instead of reloading the whole
        // grid — recolor, not just re-select, since the <select> itself
        // carries the status color.
        const map = { present: 'bg-emerald-500', absent: 'bg-rose-500', late: 'bg-amber-500', excused: 'bg-sky-500' };
        Object.values(map).forEach((cls) => select.classList.remove(cls));
        select.classList.remove('bg-neutral-200', 'dark:bg-white/10', 'text-neutral-500');
        select.classList.add(map[status], 'text-white');
    }

    async function openAttendanceHistory(classId, code) {
        historyState.classId = classId;
        DOM.attendanceHistoryClassCode.textContent = code;
        DOM.attendanceHistoryHead.innerHTML = '';
        DOM.attendanceHistoryBody.innerHTML = '<tr><td class="py-6 px-6 text-center text-neutral-400">Loading history...</td></tr>';
        DOM.attendanceHistorySessionCount.textContent = '';
        toggleModalEl(DOM.attendanceHistoryModal, DOM.attendanceHistoryModalCard, true);

        const { error, data } = await ApiService.request(CONFIG.API_ATTENDANCE_HISTORY(classId));
        if (error) {
            DOM.attendanceHistoryBody.innerHTML = `<tr><td class="py-6 px-6 text-center text-rose-500">${data?.message || 'Failed to load attendance history.'}</td></tr>`;
            return;
        }

        renderAttendanceHistory(data?.data ?? { sessions: [], students: [] });
    }

    function exportAttendanceHistory() {
        if (!historyState.classId) return;
        window.open(CONFIG.API_ATTENDANCE_HISTORY_EXPORT(historyState.classId), '_blank');
    }

    async function importAttendanceHistory(file) {
        if (!historyState.classId || !file) return;

        const body = new FormData();
        body.append('file', file);

        const { error, data } = await ApiService.request(CONFIG.API_ATTENDANCE_HISTORY_IMPORT(historyState.classId), { method: 'POST', body });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Import failed.' });
            return;
        }

        const report = data?.data?.report ?? {};
        const skippedCount = report.skipped?.length ?? 0;
        Toast.fire({
            icon: skippedCount ? 'warning' : 'success',
            title: `${report.updated_count ?? 0} record(s) updated${skippedCount ? `, ${skippedCount} row(s) skipped` : ''}.`,
        });
        if (skippedCount) {
            console.warn('Attendance history import — skipped rows:', report.skipped);
        }

        // Re-fetch so the grid reflects whatever the file just changed.
        const code = DOM.attendanceHistoryClassCode.textContent;
        openAttendanceHistory(historyState.classId, code);
    }

    async function saveScoreCell(input) {
        const enrollmentId = input.dataset.enrollmentId;
        const component = input.dataset.component;
        const points = input.value;

        if (points === input.dataset.original) return;
        if (points === '') return;

        const { error, data } = await ApiService.request(CONFIG.API_SCORES, {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ course_enrollment_id: enrollmentId, component, points }),
        });

        if (error) {
            const messages = data?.errors ? Object.values(data.errors).flat() : [data?.message || 'Failed to save score'];
            Toast.fire({ icon: 'error', title: messages[0] });
            input.value = input.dataset.original;
            recalcRowTotal(input.closest('tr'));
            return;
        }

        input.dataset.original = points;
        // keep rosterState in sync so re-filtering doesn't lose this save
        const enr = rosterState.enrollments.find((e) => String(e.id) === String(enrollmentId));
        if (enr) {
            enr.scores = (enr.scores ?? []).filter((s) => s.component !== component);
            enr.scores.push({ component, points: Number(points) });
        }
        Toast.fire({ icon: 'success', title: `${component} saved.` });
    }

    function recalcRowTotal(row) {
        const cell = row?.querySelector('.total-point-cell');
        if (cell) cell.textContent = rowTotal(row);
    }

    // ---- Attendance (QR session) ----

    function stopAttendanceTimers() {
        clearTimeout(attendanceState.qrTimer);
        clearTimeout(attendanceState.pollTimer);
        clearInterval(attendanceState.clockTimer);
        attendanceState.qrTimer = null;
        attendanceState.pollTimer = null;
        attendanceState.clockTimer = null;
    }

    function tickClock() {
        const now = new Date();
        if (DOM.attendanceClockTime) DOM.attendanceClockTime.textContent = now.toLocaleTimeString('en-GB');
        if (DOM.attendanceClockDate) DOM.attendanceClockDate.textContent = now.toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }

    async function openAttendance(classId, code) {
        stopAttendanceTimers();
        attendanceState.classId = classId;
        attendanceState.sessionId = null;
        DOM.attendanceClassCode.textContent = code;
        DOM.attendanceNoSession.classList.remove('hidden');
        DOM.attendanceLive.classList.add('hidden');
        toggleModalEl(DOM.attendanceModal, DOM.attendanceModalCard, true);
        tickClock();
        attendanceState.clockTimer = setInterval(tickClock, 1000);

        // start() is idempotent — resumes today's session if one's already
        // open, so reopening this modal shouldn't force a redundant click
        // just to see the QR/roster that's already live.
        await startSession();
    }

    function closeAttendance() {
        stopAttendanceTimers();
        toggleModalEl(DOM.attendanceModal, DOM.attendanceModalCard, false);
    }

    async function startSession(newSession = false) {
        const { error, data } = await ApiService.request(CONFIG.API_START_SESSION(attendanceState.classId), {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ new_session: newSession }),
        });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to start session.' });
            return;
        }
        attendanceState.sessionId = data.data.id;
        DOM.attendanceNoSession.classList.add('hidden');
        DOM.attendanceLive.classList.remove('hidden');
        renderSessionRoster(data.data);
        if (data.data.status === 'open') {
            refreshQrToken();
            pollSession();
        }
    }

    function startNextSession() {
        stopAttendanceTimers();
        attendanceState.clockTimer = setInterval(tickClock, 1000);
        startSession(true);
    }

    async function refreshQrToken() {
        if (!attendanceState.sessionId) return;
        const { error, data } = await ApiService.request(CONFIG.API_QR_TOKEN(attendanceState.sessionId));
        if (error) {
            // A closed/locked session (or any other rejection) means there's
            // nothing left to rotate — stop polling instead of retrying
            // every few seconds forever.
            return;
        }

        if (data?.data?.token) {
            const attendUrl = `${window.location.origin}/attend?token=${encodeURIComponent(data.data.token)}`;
            const dataUrl = await QRCode.toDataURL(attendUrl, { width: 420, margin: 1 });
            DOM.attendanceQrImg.src = dataUrl;
            // Same value a phone camera would read out of the QR above —
            // exposed here only so this page's own state is inspectable,
            // not a separate secret.
            DOM.attendanceQrImg.dataset.token = data.data.token;
            if (DOM.attendanceTestLink) DOM.attendanceTestLink.href = attendUrl;
        }

        const secondsRemaining = Math.max(1, data?.data?.seconds_remaining ?? 5);
        attendanceState.qrTimer = setTimeout(refreshQrToken, secondsRemaining * 1000);
    }

    function attendanceStatusBadge(status) {
        const styles = {
            present: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            late: 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            absent: 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
            excused: 'bg-neutral-100 text-neutral-600 dark:bg-white/5 dark:text-neutral-400',
            pending: 'bg-neutral-50 text-neutral-400 dark:bg-white/5 dark:text-neutral-500',
        };
        return `<span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide ${styles[status] ?? styles.pending}">${status}</span>`;
    }

    function initials(name) {
        return (name || '').trim().split(/\s+/).map((w) => w[0]).slice(0, 2).join('').toUpperCase() || '?';
    }

    function renderSessionRoster(session) {
        const isOpen = session.status === 'open';
        const sessionLabel = session.session_number > 1 ? `Session ${session.session_number} — ` : '';
        DOM.attendanceSessionStatus.textContent = isOpen ? `${sessionLabel}Scanning live` : `${sessionLabel}Session ${session.status}`;
        DOM.attendanceCount.textContent = `${session.present_count} / ${session.total} present`;
        DOM.attendanceSubmitBtn.classList.toggle('hidden', !isOpen);
        // A real teaching day has at most MAX_SESSIONS_PER_DAY sessions
        // (see ClassSessionController) — once that many are locked, there's
        // no legitimate next one to start, so the button just disappears
        // instead of offering a Session 3 that doesn't exist.
        const atSessionCap = (session.session_number ?? 1) >= 2;
        if (DOM.attendanceStartNextBtn) {
            DOM.attendanceStartNextBtn.classList.toggle('hidden', isOpen || atSessionCap);
            DOM.attendanceStartNextBtn.textContent = `Start Session ${(session.session_number ?? 1) + 1}`;
        }
        DOM.attendanceQrImg.closest('#attendanceQrWrap')?.classList.toggle('opacity-30', !isOpen);
        DOM.attendanceTestLink?.closest('div')?.classList.toggle('hidden', !isOpen);
        if (DOM.attendanceStatusDot) {
            DOM.attendanceStatusDot.className = `w-2 h-2 rounded-full ${isOpen ? 'bg-emerald-500 animate-pulse' : 'bg-neutral-400'}`;
        }
        const statusPillWrap = DOM.attendanceSessionStatus.closest('div');
        if (statusPillWrap) {
            statusPillWrap.className = `mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-full border ${isOpen
                ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200/70 dark:border-emerald-500/20'
                : 'bg-neutral-100 dark:bg-white/5 border-neutral-200/70 dark:border-white/10'}`;
            DOM.attendanceSessionStatus.className = `text-xs font-bold uppercase tracking-wide ${isOpen ? 'text-emerald-700 dark:text-emerald-400' : 'text-neutral-500 dark:text-neutral-400'}`;
        }

        DOM.attendanceRosterList.innerHTML = (session.roster ?? []).map((row) => `
            <div class="flex items-center justify-between px-5 py-4 text-lg hover:bg-neutral-50/70 dark:hover:bg-white/[0.03] transition-colors">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center shrink-0">${initials(row.student_name)}</div>
                    <span class="truncate font-medium">${row.student_code} — ${row.student_name || '—'}</span>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    ${attendanceStatusBadge(row.status)}
                    ${isOpen ? `
                        <select data-action="manual-mark" data-session-roster-id="${row.session_roster_id}"
                            class="text-sm px-2.5 py-2 bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg">
                            <option value="">Mark...</option>
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="absent">Absent</option>
                            <option value="excused">Excused</option>
                        </select>` : (row.attendance_record_id ? `
                        <button data-action="request-correction" data-record-id="${row.attendance_record_id}" data-current-status="${row.status}"
                            class="text-xs font-semibold text-indigo-600 hover:underline">Request Correction</button>` : '')}
                </div>
            </div>`).join('');
    }

    async function pollSession() {
        if (!attendanceState.sessionId) return;
        const { error, data } = await ApiService.request(CONFIG.API_SESSION(attendanceState.sessionId));
        if (!error) {
            renderSessionRoster(data.data);
            if (data.data.status !== 'open') {
                stopAttendanceTimers();
                return;
            }
        }
        attendanceState.pollTimer = setTimeout(pollSession, 4000);
    }

    async function manualMark(sessionRosterId, status) {
        const { error, data } = await ApiService.request(CONFIG.API_MARK(attendanceState.sessionId), {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ session_roster_id: sessionRosterId, status }),
        });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to mark.' });
            return;
        }
        renderSessionRoster(data.data);
    }

    async function submitSession() {
        const confirmation = await Swal.fire({
            title: 'End and lock this session?',
            text: 'Anyone still unmarked will be recorded absent. This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'End Session',
        });
        if (!confirmation.isConfirmed) return;

        const { error, data } = await ApiService.request(CONFIG.API_SUBMIT_SESSION(attendanceState.sessionId), { method: 'PATCH' });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to close session.' });
            return;
        }
        stopAttendanceTimers();
        renderSessionRoster(data.data);
        Toast.fire({ icon: 'success', title: 'Session closed.' });
        loadClasses();
    }

    /**
     * Queues a correction — never changes the record itself. A registrar
     * has to approve it first (see the attendance-review queue).
     */
    async function requestCorrection(recordId, currentStatus) {
        const { value: form } = await Swal.fire({
            title: 'Request a correction',
            html:
                `<select id="swal-correction-status" class="swal2-select" style="display:flex">
                    <option value="">-- new status --</option>
                    <option value="present" ${currentStatus === 'present' ? 'disabled' : ''}>Present</option>
                    <option value="late" ${currentStatus === 'late' ? 'disabled' : ''}>Late</option>
                    <option value="absent" ${currentStatus === 'absent' ? 'disabled' : ''}>Absent</option>
                    <option value="excused" ${currentStatus === 'excused' ? 'disabled' : ''}>Excused</option>
                </select>` +
                '<textarea id="swal-correction-reason" class="swal2-textarea" placeholder="Why? (e.g. phone died, approved leave, scanned for the wrong session)"></textarea>',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Send to Registrar',
            confirmButtonColor: '#4f46e5',
            preConfirm: () => {
                const newStatus = document.getElementById('swal-correction-status').value;
                const reason = document.getElementById('swal-correction-reason').value.trim();
                if (!newStatus || !reason) {
                    Swal.showValidationMessage('Pick a new status and explain why.');
                    return false;
                }
                return { new_status: newStatus, reason };
            },
        });

        if (!form) return;

        const { error, data } = await ApiService.request(`/api/v1/lecturer-portal/attendance-records/${recordId}/corrections`, {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(form),
        });

        if (error) {
            const messages = data?.errors ? Object.values(data.errors).flat() : [data?.message || 'Failed to request correction.'];
            Toast.fire({ icon: 'error', title: messages[0] });
            return;
        }

        Toast.fire({ icon: 'success', title: data?.message || 'Correction requested.' });
    }

    // ---- Wiring ----

    window.ScoreConfigModal = { toggle: (open) => toggleModalEl(DOM.scoreConfigModal, DOM.scoreConfigModalCard, open) };
    window.RosterModal = { toggle: (open) => toggleModalEl(DOM.rosterModal, DOM.rosterModalCard, open) };
    window.AttendanceHistoryModal = { toggle: (open) => toggleModalEl(DOM.attendanceHistoryModal, DOM.attendanceHistoryModalCard, open) };
    window.AttendanceModal = { toggle: (open) => (open ? toggleModalEl(DOM.attendanceModal, DOM.attendanceModalCard, true) : closeAttendance()) };

    DOM.rosterHistoryBtn?.addEventListener('click', () => openAttendanceHistory(rosterState.classId, DOM.rosterClassCode.textContent));

    DOM.scoreConfigForm?.addEventListener('submit', handleScoreConfigSubmit);
    DOM.scoreConfigForm?.addEventListener('input', (e) => {
        if (e.target.classList.contains('score-max-input')) recalcScoreTotal();
    });

    DOM.tableBody?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const id = btn.dataset.id;
        const code = btn.dataset.code;
        if (btn.dataset.action === 'score-config') openScoreConfig(id, code);
        if (btn.dataset.action === 'roster') openRoster(id, code);
        if (btn.dataset.action === 'attendance') openAttendance(id, code);
    });

    DOM.attendanceStartBtn?.addEventListener('click', () => startSession());
    DOM.attendanceSubmitBtn?.addEventListener('click', submitSession);
    DOM.attendanceStartNextBtn?.addEventListener('click', startNextSession);
    DOM.attendanceRosterList?.addEventListener('change', (e) => {
        const select = e.target.closest('select[data-action="manual-mark"]');
        if (!select || !select.value) return;
        manualMark(select.dataset.sessionRosterId, select.value);
        select.value = '';
    });
    DOM.attendanceRosterList?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action="request-correction"]');
        if (!btn) return;
        requestCorrection(btn.dataset.recordId, btn.dataset.currentStatus);
    });

    DOM.rosterTableBody?.addEventListener('blur', (e) => {
        if (!e.target.classList.contains('score-cell')) return;
        saveScoreCell(e.target);
    }, true);

    DOM.rosterTableBody?.addEventListener('input', (e) => {
        if (!e.target.classList.contains('score-cell')) return;
        recalcRowTotal(e.target.closest('tr'));
    });

    DOM.rosterSearchInput?.addEventListener('input', (e) => filterRoster(e.target.value));

    DOM.attendanceHistoryBody?.addEventListener('change', (e) => {
        if (e.target.dataset.action !== 'history-mark') return;
        markFromHistory(e.target);
    });

    DOM.attendanceHistoryExportBtn?.addEventListener('click', exportAttendanceHistory);
    DOM.attendanceHistoryImportBtn?.addEventListener('click', () => DOM.attendanceHistoryImportFile?.click());
    DOM.attendanceHistoryImportFile?.addEventListener('change', () => {
        const file = DOM.attendanceHistoryImportFile.files?.[0];
        if (file) importAttendanceHistory(file);
        DOM.attendanceHistoryImportFile.value = '';
    });

    document.addEventListener('DOMContentLoaded', loadClasses);
})();
