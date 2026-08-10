/**
 * Public exam-attendance page — no auth. Lets on-site staff search for a room
 * and record/update the absent headcount for the current session (round).
 * Reuses the existing /api/v1/exam-states endpoints (already unauthenticated).
 */

const API_BASE = '/api/v1/exam-states';
const round = window.EXAM_ROUND; // 1, 2, or 3
const roundIndex = round - 1;

// Fixed session times, positionally matched to rounds 1/2/3.
const ROUND_TIMES = ['08:00 AM', '10:00 AM', '01:00 PM'];

const els = {
    search: document.getElementById('roomSearchInput'),
    list: document.getElementById('roomList'),
};

let currentRooms = [];
let searchTimer = null;

function currentAbsent(room) {
    return room.absences?.[roundIndex]?.total ?? null;
}

async function fetchRooms(search = '') {
    const res = await fetch(`${API_BASE}?search=${encodeURIComponent(search)}`, {
        headers: { Accept: 'application/json' },
    });
    const json = await res.json();
    return Array.isArray(json.data) ? json.data : [];
}

function renderList(rooms) {
    if (!rooms.length) {
        els.list.innerHTML = '<p class="text-center text-neutral-400 py-10">រកមិនឃើញបន្ទប់ទេ (No rooms found)</p>';
        return;
    }

    els.list.innerHTML = rooms.map((room) => {
        const absent = currentAbsent(room);
        const statusBadge = absent === null
            ? '<span class="px-2.5 py-1 text-xs font-bold rounded-full bg-neutral-100 text-neutral-500 dark:bg-white/5 dark:text-neutral-400">មិនទាន់បញ្ចូល</span>'
            : `<span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">អវត្តមាន ${absent}</span>`;

        return `
            <div class="flex items-center justify-between gap-3 p-4 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl">
                <div class="min-w-0">
                    <div class="font-bold text-neutral-900 dark:text-white">បន្ទប់ ${room.room ?? 'N/A'}</div>
                    <div class="text-xs text-neutral-400 truncate">${room.major ?? ''} · ${room.degree ?? ''} · និស្សិត ${room.student_total ?? 0} នាក់</div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    ${statusBadge}
                    <button data-id="${room.id}" class="enter-absence-btn px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                        បញ្ចូល/កែប្រែ
                    </button>
                </div>
            </div>`;
    }).join('');
}

async function loadRooms(search = '') {
    els.list.innerHTML = '<p class="text-center text-neutral-400 py-10">កំពុងទាញយក...</p>';
    currentRooms = await fetchRooms(search);
    renderList(currentRooms);
}

async function openAbsenceModal(id) {
    const room = currentRooms.find((r) => String(r.id) === String(id));
    if (!room) return;

    const studentTotal = room.student_total ?? 0;

    const { value: total, isConfirmed } = await Swal.fire({
        title: `បន្ទប់ ${room.room} — ${window.EXAM_ROUND_LABEL} (${ROUND_TIMES[roundIndex]})`,
        input: 'number',
        inputLabel: `ចំនួនអវត្តមាន (Absent count) — និស្សិតសរុប ${studentTotal} នាក់`,
        inputValue: currentAbsent(room) ?? 0,
        inputAttributes: { min: 0, max: studentTotal },
        showCancelButton: true,
        confirmButtonText: 'រក្សាទុក',
        cancelButtonText: 'បោះបង់',
        inputValidator: (value) => {
            const n = Number(value);
            if (value === '' || Number.isNaN(n) || n < 0) {
                return 'សូមបញ្ចូលលេខត្រឹមត្រូវ (Enter a valid number)';
            }
            if (n > studentTotal) {
                return `ចំនួនអវត្តមានមិនអាចលើសពីចំនួននិស្សិតសរុប (${studentTotal}) បានទេ`;
            }
        },
    });

    if (!isConfirmed || total === undefined || total === '') return;

    const absences = Array.isArray(room.absences) ? [...room.absences] : [];
    while (absences.length < 3) absences.push({ total: 0 });
    absences[roundIndex] = {
        major: room.major,
        time: ROUND_TIMES[roundIndex],
        total: Number(total) || 0,
    };

    const res = await fetch(`${API_BASE}/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({
            room: room.room,
            shift: room.shift,
            major: room.major,
            degree: room.degree,
            student_total: room.student_total,
            majors: room.majors,
            exam_date: room.exam_date,
            remark: room.remark,
            absences,
        }),
    });

    if (res.ok) {
        Swal.fire({ icon: 'success', title: 'រក្សាទុកបានជោគជ័យ!', timer: 1500, showConfirmButton: false });
        loadRooms(els.search?.value || '');
    } else {
        const body = await res.json().catch(() => ({}));
        const firstError = body.errors ? Object.values(body.errors)[0]?.[0] : null;
        Swal.fire({ icon: 'error', title: firstError || body.message || 'មានបញ្ហាកើតឡើង' });
    }
}

els.search?.addEventListener('input', (e) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadRooms(e.target.value), 300);
});

els.list?.addEventListener('click', (e) => {
    const btn = e.target.closest('.enter-absence-btn');
    if (btn) openAbsenceModal(btn.dataset.id);
});

loadRooms();
