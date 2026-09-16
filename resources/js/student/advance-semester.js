/**
 * Advance Semester — a focused modal (just the academic fields) for moving
 * one student to a new batch/year/etc., instead of the full edit-student
 * form. Posts to PATCH /students/{id}/advance-semester, which creates a new
 * student_academic_histories row only for fields that actually changed and
 * leaves the previous semester's record untouched (see StudentController's
 * advanceAcademicHistory()).
 */
import { CONFIG } from './config.js';
import { fillSelectOptions, loadTermOptions } from './form-utils.js';
import { getById } from '../app.js';
import { registerModalCloser } from './ui.js';

let lookupsLoaded = false;

function toggle(forceOpen = null) {
    const modal = getById('advanceSemesterModal');
    const card = getById('advanceSemesterCard');
    if (!modal || !card) return;

    const isOpen = modal.classList.contains('flex');
    const makeOpen = forceOpen !== null ? forceOpen : !isOpen;

    if (makeOpen) {
        modal.classList.remove('invisible');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-90', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        });
    } else {
        modal.classList.add('opacity-0');
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-90', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('invisible');
            modal.classList.remove('flex');
        }, 300);
    }
}

async function loadLookupsOnce(ApiService) {
    if (lookupsLoaded) return;
    // These endpoints paginate at 10/page by default — fine for a data
    // table, wrong for a dropdown that needs every row (e.g. batch #14
    // silently has no <option> otherwise). Ask for all of them.
    const withAll = (url) => `${url}${url.includes('?') ? '&' : '?'}per_page=1000`;
    const [batches, majors, campuses, shifts, groups, statuses] = await Promise.all([
        ApiService.request(withAll(CONFIG.API_LOOKUPS.batches)),
        ApiService.request(withAll(CONFIG.API_LOOKUPS.majors)),
        ApiService.request(withAll(CONFIG.API_LOOKUPS.campuses)),
        ApiService.request(withAll(CONFIG.API_LOOKUPS.shifts)),
        ApiService.request(withAll(CONFIG.API_LOOKUPS.groups)),
        ApiService.request(withAll(CONFIG.API_LOOKUPS.statuses)),
    ]);

    const fill = (res, id) => {
        if (!res.error) fillSelectOptions(getById(id), res.data?.data ?? res.data ?? []);
    };
    fill(batches, 'advance_batch_id');
    fill(majors, 'advance_major_id');
    fill(shifts, 'advance_shift_id');
    fill(groups, 'advance_group_id');
    fill(statuses, 'advance_status_id');

    // Campus keeps its own blank placeholder (campus is optional).
    if (!campuses.error) {
        const el = getById('advance_campus_id');
        if (el) {
            const items = campuses.data?.data ?? campuses.data ?? [];
            el.innerHTML = '<option value="">—</option>';
            items.forEach((item) => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.name_kh || item.name || item.name_en;
                el.appendChild(opt);
            });
        }
    }

    lookupsLoaded = true;
}

export function initAdvanceSemester(ApiService, onAdvanced) {
    registerModalCloser('advance-semester', () => toggle(false));

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('button[data-action="advance"]');
        if (!btn) return;

        const studentId = btn.getAttribute('data-id');
        await loadLookupsOnce(ApiService);
        await loadTermOptions(ApiService, getById('advanceSemesterTermId'));

        const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${studentId}`);
        if (error) return;
        const student = data.data || data;

        const nameEl = getById('advanceSemesterStudentName');
        if (nameEl) {
            const person = student.person ?? {};
            nameEl.textContent = `${person.first_name ?? ''} ${person.last_name ?? ''} · ${student.code ?? ''}`.trim();
        }

        const form = getById('advanceSemesterForm');
        if (form) {
            form.dataset.studentId = studentId;
            const setSelect = (id, value) => { const el = getById(id); if (el) el.value = value?.id ?? ''; };
            setSelect('advance_batch_id', student.batch);
            setSelect('advance_major_id', student.major);
            setSelect('advance_shift_id', student.shift);
            setSelect('advance_group_id', student.group);
            setSelect('advance_campus_id', student.campus);
            setSelect('advance_status_id', student.status);
            const yearLevelEl = getById('advance_year_level');
            if (yearLevelEl) yearLevelEl.value = student.year_level ?? 1;
            const semesterEl = getById('advance_semester');
            if (semesterEl) semesterEl.value = student.semester ?? '';
        }

        toggle(true);
    });

    getById('advanceSemesterForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const studentId = form.dataset.studentId;
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        const payload = {
            batch_id: getById('advance_batch_id').value,
            major_id: getById('advance_major_id').value,
            shift_id: getById('advance_shift_id').value,
            group_id: getById('advance_group_id').value,
            campus_id: getById('advance_campus_id').value || null,
            status_id: getById('advance_status_id').value,
            year_level: getById('advance_year_level').value,
            semester: getById('advance_semester').value || null,
            term_id: getById('advanceSemesterTermId')?.value || null,
        };

        const { error, status, data } = await ApiService.request(`${CONFIG.API_BASE}/${studentId}/advance-semester`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!error) {
            window.Swal?.fire({
                icon: 'success',
                title: 'Advanced!',
                text: 'The student’s new semester has been recorded.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
            });
            toggle(false);
            onAdvanced?.();
        } else {
            const message = status === 422
                ? Object.values(data?.errors ?? {}).flat().join(' ')
                : (data?.message || 'Could not advance this student.');
            window.Swal?.fire({ icon: 'error', title: 'Error', text: message });
        }
        submitBtn.disabled = false;
    });
}
