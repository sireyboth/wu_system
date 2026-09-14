/**
 * Classes page — registrar-facing: create classes (optionally assigning
 * a primary lecturer right away), auto-enroll students by filter
 * (mixed-major just means leaving Major blank), assign/reassign lecturers,
 * and record scores against the roster. Score CONFIG (each lecturer's own
 * point split) deliberately isn't here — that's the lecturer's own
 * setting, not something a registrar enters on their behalf; it stays a
 * backend-only endpoint (ClassSectionController::scoreConfig) until a
 * lecturer-facing portal exists to own it. Self-contained IIFE, same
 * style as resources/js/status/status.js and term/term.js.
 */
(() => {
    'use strict';

    const CONFIG = {
        API_CLASSES: '/api/v1/classes',
        API_SUBJECTS: '/api/v1/subjects',
        API_TERMS: '/api/v1/terms',
        API_CAMPUSES: '/api/v1/campuses',
        API_SHIFTS: '/api/v1/shifts',
        API_BATCHES: '/api/v1/batches',
        API_MAJORS: '/api/v1/majors',
        API_GROUPS: '/api/v1/groups',
        API_STATUSES: '/api/v1/statuses',
        API_COURSE_ENROLLMENTS: '/api/v1/course-enrollments',
        API_CLASS_SCORES: '/api/v1/class-scores',
        API_LECTURERS: '/api/v1/lecturers',
        API_TEACHER_ASSIGNMENTS: '/api/v1/teacher-assignments',
        DEBOUNCE_DELAY: 300,
    };

    const Toast = typeof Swal !== 'undefined' ? Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    }) : { fire: console.log };

    const DOM = {
        loader: document.getElementById('loading-overlay'),
        tableBody: document.getElementById('class-table-body'),
        searchInput: document.getElementById('classSearchInput'),

        classForm: document.getElementById('classForm'),
        classModal: document.getElementById('classModal'),
        classModalCard: document.getElementById('classModalCard'),
        classModalTitle: document.getElementById('classModalTitle'),
        classSubmitBtn: document.getElementById('classSubmitBtn'),
        subjectSearch: document.getElementById('classSubjectSearch'),
        subjectDatalist: document.getElementById('subjectsDatalist'),
        subjectId: document.getElementById('classSubjectId'),
        subjectHint: document.getElementById('classSubjectHint'),
        termSelect: document.getElementById('classTermSelect'),
        campusSelect: document.getElementById('classCampusSelect'),
        shiftSelect: document.getElementById('classShiftSelect'),
        lecturerSelect: document.getElementById('classLecturerSelect'),
        lecturerField: document.getElementById('classLecturerField'),
        lecturerEditHint: document.getElementById('classLecturerEditHint'),

        autoEnrollForm: document.getElementById('autoEnrollForm'),
        autoEnrollModal: document.getElementById('autoEnrollModal'),
        autoEnrollModalCard: document.getElementById('autoEnrollModalCard'),
        autoEnrollClassCode: document.getElementById('autoEnrollClassCode'),
        autoEnrollClassId: document.getElementById('autoEnrollClassId'),
        autoEnrollResult: document.getElementById('autoEnrollResult'),

        rosterModal: document.getElementById('rosterModal'),
        rosterModalCard: document.getElementById('rosterModalCard'),
        rosterClassCode: document.getElementById('rosterClassCode'),
        rosterTableBody: document.getElementById('rosterTableBody'),
        rosterSearchInput: document.getElementById('rosterSearchInput'),

        assignLecturerForm: document.getElementById('assignLecturerForm'),
        assignLecturerModal: document.getElementById('assignLecturerModal'),
        assignLecturerModalCard: document.getElementById('assignLecturerModalCard'),
        assignLecturerClassCode: document.getElementById('assignLecturerClassCode'),
        assignLecturerClassId: document.getElementById('assignLecturerClassId'),
        assignLecturerSelect: document.getElementById('assignLecturerSelect'),
        assignLecturerExisting: document.getElementById('assignLecturerExisting'),
    };

    const state = { subjects: [], classes: [], debounceTimer: null, currentClassId: null, editingClassId: null };
    const rosterState = { enrollments: [] };

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

    function fillSelect(el, items, placeholder = '-- any --') {
        if (!el) return;
        el.innerHTML = `<option value="">${placeholder}</option>` +
            items.map((item) => `<option value="${item.id}">${item.name_kh || item.name || item.code}</option>`).join('');
    }

    async function loadLookups() {
        const [terms, campuses, shifts, batches, majors, groups, statuses, subjects, lecturers] = await Promise.all([
            ApiService.request(`${CONFIG.API_TERMS}?per_page=200`),
            ApiService.request(`${CONFIG.API_CAMPUSES}?per_page=200`),
            ApiService.request(`${CONFIG.API_SHIFTS}?per_page=200`),
            ApiService.request(`${CONFIG.API_BATCHES}?per_page=200`),
            ApiService.request(`${CONFIG.API_MAJORS}?per_page=500`),
            ApiService.request(`${CONFIG.API_GROUPS}?per_page=200`),
            ApiService.request(`${CONFIG.API_STATUSES}?per_page=200`),
            ApiService.request(`${CONFIG.API_SUBJECTS}?per_page=2000`),
            ApiService.request(`${CONFIG.API_LECTURERS}?per_page=1000`),
        ]);

        const list = (r) => r.data?.data ?? [];

        DOM.termSelect.innerHTML = '<option value="" disabled selected>-- select term --</option>' +
            list(terms).map((t) => `<option value="${t.id}">${t.code} — ${t.name}${t.is_active ? ' (active)' : ''}</option>`).join('');
        fillSelect(DOM.campusSelect, list(campuses));
        fillSelect(DOM.shiftSelect, list(shifts));
        fillSelect(document.getElementById('autoEnrollBatch'), list(batches));
        fillSelect(document.getElementById('autoEnrollMajor'), list(majors));
        fillSelect(document.getElementById('autoEnrollShift'), list(shifts));
        fillSelect(document.getElementById('autoEnrollGroup'), list(groups));
        fillSelect(document.getElementById('autoEnrollCampus'), list(campuses));
        fillSelect(document.getElementById('autoEnrollStatus'), list(statuses));

        const lecturerOptionsHtml = list(lecturers)
            .map((l) => `<option value="${l.id}">${l.code ? `${l.code} — ` : ''}${l.name_kh || l.name_en || l.name}</option>`)
            .join('');
        DOM.assignLecturerSelect.innerHTML = '<option value="" disabled selected>-- select lecturer --</option>' + lecturerOptionsHtml;
        DOM.lecturerSelect.innerHTML = '<option value="">-- none yet --</option>' + lecturerOptionsHtml;

        state.subjects = list(subjects);
        DOM.subjectDatalist.innerHTML = state.subjects
            .map((s) => `<option value="${s.code} — ${s.name_en || s.name}" data-id="${s.id}"></option>`)
            .join('');
    }

    DOM.subjectSearch?.addEventListener('input', () => {
        const typed = DOM.subjectSearch.value;
        const match = state.subjects.find((s) => `${s.code} — ${s.name_en || s.name}` === typed);
        DOM.subjectId.value = match ? match.id : '';
        DOM.subjectHint?.classList.toggle('hidden', Boolean(match) || typed === '');
    });

    // ---- Classes list ----

    async function loadClasses(search = '') {
        const { error, data } = await ApiService.request(`${CONFIG.API_CLASSES}?search=${encodeURIComponent(search)}&per_page=50`);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load classes' });
            return;
        }
        state.classes = data?.data ?? [];
        renderTable(state.classes);
    }

    function lecturerBadge(assignments) {
        const active = (assignments ?? []).filter((a) => a.id);
        if (!active.length) {
            return '<span class="text-xs italic text-neutral-400">Not assigned</span>';
        }
        return active.map((a) => {
            const label = a.lecturer?.code ? `${a.lecturer.code} — ${a.lecturer.name_kh || a.lecturer.name_en || ''}` : (a.lecturer?.name_kh || a.lecturer?.name_en || '—');
            const roleColor = a.role === 'substitute' ? 'text-amber-600' : 'text-neutral-700 dark:text-neutral-300';
            return `<div class="text-xs ${roleColor}">${label}${a.role === 'substitute' ? ' <span class="italic">(sub)</span>' : ''}</div>`;
        }).join('');
    }

    function renderTable(classes) {
        if (!DOM.tableBody) return;
        if (!classes.length) {
            DOM.tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-neutral-500">No classes yet.</td></tr>';
            return;
        }

        DOM.tableBody.innerHTML = classes.map((cls, i) => `
            <tr class="hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 border-b border-neutral-100 dark:border-white/5">
                <td class="px-6 py-4 text-neutral-500 font-mono text-sm">${i + 1}</td>
                <td class="px-6 py-4 font-mono font-bold text-indigo-600">${cls.code}</td>
                <td class="px-6 py-4 text-sm">${cls.subject?.code ?? ''} <span class="text-neutral-400">${cls.subject?.name_en ?? ''}</span></td>
                <td class="px-6 py-4 text-sm">${cls.term?.code ?? ''}</td>
                <td class="px-6 py-4">${lecturerBadge(cls.teacher_assignments)}</td>
                <td class="px-6 py-4 text-center text-sm font-bold">${cls.enrolled_count ?? 0}</td>
                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                    <button data-action="assign-lecturer" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-sky-600 hover:bg-sky-100 dark:hover:bg-sky-500/20 rounded-lg">Lecturer</button>
                    <button data-action="auto-enroll" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-teal-600 hover:bg-teal-100 dark:hover:bg-teal-500/20 rounded-lg">Auto-Enroll</button>
                    <button data-action="roster" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 dark:hover:bg-white/10 rounded-lg">Roster</button>
                    <button data-action="edit" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-100 dark:hover:bg-amber-500/20 rounded-lg">Edit</button>
                    <button data-action="delete" data-id="${cls.id}" data-code="${cls.code}" class="px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg">Delete</button>
                </td>
            </tr>`).join('');
    }

    // ---- Create / edit class ----

    function resetClassForm() {
        DOM.classForm.reset();
        DOM.subjectId.value = '';
        DOM.subjectHint?.classList.add('hidden');
        state.editingClassId = null;
        if (DOM.classModalTitle) DOM.classModalTitle.textContent = 'Create Class';
        if (DOM.classSubmitBtn) DOM.classSubmitBtn.textContent = 'Save';
        DOM.lecturerField?.classList.remove('hidden');
        DOM.lecturerEditHint?.classList.add('hidden');
    }

    async function openEditClass(classId) {
        const { error, data } = await ApiService.request(`${CONFIG.API_CLASSES}/${classId}`);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load class.' });
            return;
        }

        const cls = data.data;
        state.editingClassId = classId;
        if (DOM.classModalTitle) DOM.classModalTitle.textContent = `Edit Class — ${cls.code}`;
        if (DOM.classSubmitBtn) DOM.classSubmitBtn.textContent = 'Update';

        DOM.subjectSearch.value = cls.subject ? `${cls.subject.code} — ${cls.subject.name_en || cls.subject.name}` : '';
        DOM.subjectId.value = cls.subject?.id ?? '';
        DOM.termSelect.value = cls.term?.id ?? '';
        DOM.campusSelect.value = cls.campus?.id ?? '';
        DOM.shiftSelect.value = cls.shift?.id ?? '';
        DOM.classForm.querySelector('[name="code"]').value = cls.code ?? '';
        DOM.classForm.querySelector('[name="capacity"]').value = cls.capacity ?? '';

        // Lecturer is create-only here (see store()) — editing has its own
        // action (the Lecturer button), so this field is hidden, not just
        // disabled, to avoid implying an edit here would reassign anyone.
        DOM.lecturerField?.classList.add('hidden');
        DOM.lecturerEditHint?.classList.remove('hidden');

        toggleModalEl(DOM.classModal, DOM.classModalCard, true);
    }

    async function handleClassSubmit(e) {
        e.preventDefault();
        if (!DOM.subjectId.value) {
            DOM.subjectHint?.classList.remove('hidden');
            Toast.fire({ icon: 'warning', title: 'Pick a subject from the dropdown list.' });
            return;
        }

        const isEdit = Boolean(state.editingClassId);

        const payload = {
            subject_id: DOM.subjectId.value,
            term_id: DOM.termSelect.value,
            code: DOM.classForm.querySelector('[name="code"]').value.trim(),
            campus_id: DOM.campusSelect.value || null,
            shift_id: DOM.shiftSelect.value || null,
            capacity: DOM.classForm.querySelector('[name="capacity"]').value || null,
        };
        if (!isEdit) {
            payload.lecturer_id = DOM.lecturerSelect.value || null;
        }

        const url = isEdit ? `${CONFIG.API_CLASSES}/${state.editingClassId}` : CONFIG.API_CLASSES;
        const { error, data } = await ApiService.request(url, {
            method: isEdit ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload),
        });

        if (error) {
            const messages = data?.errors ? Object.values(data.errors).flat() : [data?.message || 'Failed to save class'];
            Toast.fire({ icon: 'error', title: messages[0] });
            return;
        }

        Toast.fire({ icon: 'success', title: isEdit ? `Class ${data.data.code} updated.` : `Class ${data.data.code} created.` });
        resetClassForm();
        toggleModalEl(DOM.classModal, DOM.classModalCard, false);
        loadClasses(DOM.searchInput?.value || '');
    }

    async function handleDeleteClass(classId, code) {
        const confirmation = await Swal.fire({
            title: `Delete class ${code}?`,
            text: 'This moves the class to trash. Any enrollments, scores, or config tied to it stay in the database, just orphaned from view — this cannot be undone from this screen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Delete',
        });
        if (!confirmation.isConfirmed) return;

        const { error, data } = await ApiService.request(`${CONFIG.API_CLASSES}/${classId}`, { method: 'DELETE' });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to delete class.' });
            return;
        }

        Toast.fire({ icon: 'success', title: `Class ${code} deleted.` });
        loadClasses(DOM.searchInput?.value || '');
    }

    // ---- Auto-enroll ----

    function openAutoEnroll(classId, code) {
        DOM.autoEnrollClassId.value = classId;
        DOM.autoEnrollClassCode.textContent = code;
        DOM.autoEnrollForm.reset();
        DOM.autoEnrollResult.classList.add('hidden');
        toggleModalEl(DOM.autoEnrollModal, DOM.autoEnrollModalCard, true);
    }

    async function handleAutoEnrollSubmit(e) {
        e.preventDefault();
        const classId = DOM.autoEnrollClassId.value;
        const filters = {};
        ['batch_id', 'major_id', 'shift_id', 'group_id', 'campus_id', 'status_id', 'semester', 'year_level'].forEach((field) => {
            const el = DOM.autoEnrollForm.querySelector(`[name="${field}"]`);
            if (el?.value) filters[field] = el.value;
        });

        const { error, data } = await ApiService.request(`${CONFIG.API_CLASSES}/${classId}/auto-enroll`, {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ filters }),
        });

        DOM.autoEnrollResult.classList.remove('hidden');
        if (error) {
            DOM.autoEnrollResult.className = 'px-4 py-3 rounded-xl border text-sm border-rose-300 bg-rose-50 text-rose-700';
            DOM.autoEnrollResult.textContent = data?.message || 'Auto-enroll failed.';
            return;
        }

        DOM.autoEnrollResult.className = 'px-4 py-3 rounded-xl border text-sm border-emerald-300 bg-emerald-50 text-emerald-700';
        DOM.autoEnrollResult.textContent = data.message;
        loadClasses(DOM.searchInput?.value || '');
    }

    // ---- Assign lecturer ----

    function renderExistingAssignments(classId, assignments) {
        const active = (assignments ?? []).filter((a) => a.id);
        if (!active.length) {
            DOM.assignLecturerExisting.classList.add('hidden');
            DOM.assignLecturerExisting.innerHTML = '';
            return;
        }

        DOM.assignLecturerExisting.classList.remove('hidden');
        DOM.assignLecturerExisting.innerHTML = `<p class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-1">Currently assigned</p>` +
            active.map((a) => `
                <div class="flex items-center justify-between px-3 py-2 bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-sm">
                    <span>${a.lecturer?.code ? `${a.lecturer.code} — ` : ''}${a.lecturer?.name_kh || a.lecturer?.name_en || '—'} <span class="text-xs text-neutral-400 uppercase">${a.role}</span></span>
                    <button data-action="remove-assignment" data-assignment-id="${a.id}" class="text-xs font-semibold text-rose-600 hover:underline">Remove</button>
                </div>`).join('');
    }

    async function openAssignLecturer(classId, code, assignments) {
        DOM.assignLecturerClassId.value = classId;
        DOM.assignLecturerClassCode.textContent = code;
        DOM.assignLecturerForm.reset();
        DOM.assignLecturerForm.querySelector('[name="assigned_from"]').value = new Date().toISOString().slice(0, 10);
        renderExistingAssignments(classId, assignments);
        toggleModalEl(DOM.assignLecturerModal, DOM.assignLecturerModalCard, true);
    }

    async function handleAssignLecturerSubmit(e) {
        e.preventDefault();
        const payload = {
            class_id: DOM.assignLecturerClassId.value,
            lecturer_id: DOM.assignLecturerSelect.value,
            role: DOM.assignLecturerForm.querySelector('[name="role"]').value,
            assigned_from: DOM.assignLecturerForm.querySelector('[name="assigned_from"]').value,
            assigned_to: DOM.assignLecturerForm.querySelector('[name="assigned_to"]').value || null,
        };

        const { error, data } = await ApiService.request(CONFIG.API_TEACHER_ASSIGNMENTS, {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload),
        });

        if (error) {
            const messages = data?.errors ? Object.values(data.errors).flat() : [data?.message || 'Failed to assign lecturer'];
            Toast.fire({ icon: 'error', title: messages[0] });
            return;
        }

        Toast.fire({ icon: 'success', title: 'Lecturer assigned.' });
        toggleModalEl(DOM.assignLecturerModal, DOM.assignLecturerModalCard, false);
        loadClasses(DOM.searchInput?.value || '');
    }

    async function removeAssignment(assignmentId) {
        const { error, data } = await ApiService.request(`${CONFIG.API_TEACHER_ASSIGNMENTS}/${assignmentId}`, { method: 'DELETE' });
        if (error) {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to remove assignment.' });
            return;
        }
        Toast.fire({ icon: 'success', title: 'Assignment removed.' });
        toggleModalEl(DOM.assignLecturerModal, DOM.assignLecturerModalCard, false);
        loadClasses(DOM.searchInput?.value || '');
    }

    // ---- Roster ----

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
        let total = 0;
        row.querySelectorAll('.score-cell').forEach((input) => { total += Number(input.value) || 0; });
        return total;
    }

    function rowTotalFromEnrollment(enr) {
        return (enr.scores ?? []).reduce((sum, s) => sum + Number(s.points ?? 0), 0);
    }

    // Total Point is a live sum of the five score cells (saved or not) —
    // Grade Point is a placeholder column until the grading scale/formula
    // is decided, so it stays a dash for now.
    function renderRosterRow(enr) {
        return `
            <tr data-enrollment-id="${enr.id}">
                <td class="py-3 pr-4 font-medium">${studentLabel(enr)}</td>
                <td class="py-3 pr-4 text-xs uppercase text-neutral-500">${enr.status}</td>
                ${SCORE_COMPONENTS.map((c) => scoreCell(enr.id, c, enr)).join('')}
                <td class="py-3 pr-3 text-center font-bold total-point-cell">${rowTotalFromEnrollment(enr)}</td>
                <td class="py-3 text-center text-neutral-400 grade-point-cell">—</td>
            </tr>`;
    }

    function renderRosterRows(enrollments) {
        if (!enrollments.length) {
            DOM.rosterTableBody.innerHTML = '<tr><td colspan="9" class="py-6 text-center text-neutral-400">No students match your search.</td></tr>';
            return;
        }
        DOM.rosterTableBody.innerHTML = enrollments.map(renderRosterRow).join('');
    }

    async function openRoster(classId, code) {
        state.currentClassId = classId;
        DOM.rosterClassCode.textContent = code;
        if (DOM.rosterSearchInput) DOM.rosterSearchInput.value = '';
        DOM.rosterTableBody.innerHTML = '<tr><td colspan="9" class="py-6 text-center text-neutral-400">Loading roster...</td></tr>';
        toggleModalEl(DOM.rosterModal, DOM.rosterModalCard, true);

        const { error, data } = await ApiService.request(`${CONFIG.API_COURSE_ENROLLMENTS}?class_id=${classId}&per_page=200`);
        if (error) {
            DOM.rosterTableBody.innerHTML = '<tr><td colspan="9" class="py-6 text-center text-rose-500">Failed to load roster.</td></tr>';
            return;
        }

        rosterState.enrollments = data?.data ?? [];
        if (!rosterState.enrollments.length) {
            DOM.rosterTableBody.innerHTML = '<tr><td colspan="9" class="py-6 text-center text-neutral-400">No students enrolled yet — use Auto-Enroll.</td></tr>';
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

    function recalcRowTotal(row) {
        const cell = row?.querySelector('.total-point-cell');
        if (cell) cell.textContent = rowTotal(row);
    }

    async function saveScoreCell(input) {
        const enrollmentId = input.dataset.enrollmentId;
        const component = input.dataset.component;
        const points = input.value;

        if (points === input.dataset.original) return;
        if (points === '') return;

        const { error, data } = await ApiService.request(CONFIG.API_CLASS_SCORES, {
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
        const enr = rosterState.enrollments.find((e) => String(e.id) === String(enrollmentId));
        if (enr) {
            enr.scores = (enr.scores ?? []).filter((s) => s.component !== component);
            enr.scores.push({ component, points: Number(points) });
        }
        Toast.fire({ icon: 'success', title: `${component} saved.` });
    }

    // ---- Wiring ----

    window.ClassModal = {
        toggle: (open) => {
            toggleModalEl(DOM.classModal, DOM.classModalCard, open);
            if (!open) resetClassForm();
        },
    };
    window.AutoEnrollModal = { toggle: (open) => toggleModalEl(DOM.autoEnrollModal, DOM.autoEnrollModalCard, open) };
    window.RosterModal = { toggle: (open) => toggleModalEl(DOM.rosterModal, DOM.rosterModalCard, open) };
    window.AssignLecturerModal = { toggle: (open) => toggleModalEl(DOM.assignLecturerModal, DOM.assignLecturerModalCard, open) };

    DOM.classForm?.addEventListener('submit', handleClassSubmit);
    DOM.autoEnrollForm?.addEventListener('submit', handleAutoEnrollSubmit);

    DOM.tableBody?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const id = btn.dataset.id;
        const code = btn.dataset.code;
        if (btn.dataset.action === 'auto-enroll') openAutoEnroll(id, code);
        if (btn.dataset.action === 'roster') openRoster(id, code);
        if (btn.dataset.action === 'edit') openEditClass(id);
        if (btn.dataset.action === 'delete') handleDeleteClass(id, code);
        if (btn.dataset.action === 'assign-lecturer') {
            const cls = state.classes.find((c) => String(c.id) === String(id));
            openAssignLecturer(id, code, cls?.teacher_assignments);
        }
    });

    DOM.assignLecturerForm?.addEventListener('submit', handleAssignLecturerSubmit);
    DOM.assignLecturerExisting?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action="remove-assignment"]');
        if (!btn) return;
        removeAssignment(btn.dataset.assignmentId);
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

    DOM.searchInput?.addEventListener('input', (e) => {
        clearTimeout(state.debounceTimer);
        state.debounceTimer = setTimeout(() => loadClasses(e.target.value), CONFIG.DEBOUNCE_DELAY);
    });

    document.addEventListener('DOMContentLoaded', async () => {
        await loadLookups();
        loadClasses();
    });
})();
