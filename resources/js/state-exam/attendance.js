/**
 * Public exam-attendance page — no auth. Lets on-site staff search for a room
 * and record/update the absent headcount for the current session (round).
 * Reuses the existing /api/v1/exam-states endpoints (already unauthenticated).
 *
 * Fully self-contained: no SweetAlert / admin-module dependency — this page
 * has its own modal + toast so the public UI can look and behave however it
 * needs to without touching the admin experience.
 */

import { baseUri } from "../app";

const API_BASE = baseUri('exam-states');
const round = window.EXAM_ROUND; // 1, 2, or 3
const roundIndex = round - 1;

// Fixed session times, positionally matched to rounds 1/2/3.
const ROUND_TIMES = ['08:00 AM', '10:00 AM', '01:00 PM'];

const els = {
    search: document.getElementById('roomSearchInput'),
    list: document.getElementById('roomList'),
    toastStack: document.getElementById('stateToastStack'),

    modal: document.getElementById('absenceModal'),
    modalCard: document.getElementById('absenceModalCard'),
    modalTitle: document.getElementById('absenceModalTitle'),
    modalSubtitle: document.getElementById('absenceModalSubtitle'),
    input: document.getElementById('absenceInput'),
    maxHint: document.getElementById('absenceMaxHint'),
    error: document.getElementById('absenceError'),
    minusBtn: document.getElementById('absenceMinusBtn'),
    plusBtn: document.getElementById('absencePlusBtn'),
    saveBtn: document.getElementById('absenceSaveBtn'),
    cancelBtn: document.getElementById('absenceCancelBtn'),
    closeBtn: document.getElementById('absenceCloseBtn'),
};

let currentRooms = [];
let searchTimer = null;
let activeRoom = null;

function currentAbsent(room) {
    return room.absences?.[roundIndex]?.total ?? null;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}

// ---------- Toast ----------

function showToast(type, message) {
    if (!els.toastStack) return;

    const palette = {
        success: { bg: 'bg-emerald-600', icon: 'M4.5 12.75l6 6 9-13.5' },
        error: { bg: 'bg-rose-600', icon: 'M6 18L18 6M6 6l12 12' },
    }[type] ?? { bg: 'bg-neutral-800', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' };

    const toast = document.createElement('div');
    toast.className = `flex items-center gap-3 px-4 py-3.5 rounded-2xl shadow-2xl text-white text-sm font-bold ${palette.bg} translate-x-6 opacity-0 transition-all duration-300`;
    toast.innerHTML = `
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="${palette.icon}" />
        </svg>
        <span>${escapeHtml(message)}</span>`;

    els.toastStack.appendChild(toast);
    requestAnimationFrame(() => toast.classList.remove('translate-x-6', 'opacity-0'));

    setTimeout(() => {
        toast.classList.add('translate-x-6', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 2600);
}

// ---------- List rendering ----------

function emptyState() {
    return `
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-neutral-100 dark:bg-white/5 text-neutral-300 dark:text-neutral-600 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">រកមិនឃើញបន្ទប់ទេ</p>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">No rooms match your search</p>
        </div>`;
}

function skeletonRows(n = 4) {
    return Array.from({ length: n }).map((_, i) => `
        <div class="flex items-center justify-between gap-3 p-4 bg-white/60 dark:bg-neutral-900/50 border border-neutral-200/70 dark:border-white/5 rounded-2xl animate-pulse" style="animation-delay:${i * 80}ms">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-11 h-11 rounded-xl bg-neutral-200 dark:bg-white/10 shrink-0"></div>
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="h-3.5 w-24 bg-neutral-200 dark:bg-white/10 rounded"></div>
                    <div class="h-3 w-40 bg-neutral-100 dark:bg-white/5 rounded"></div>
                </div>
            </div>
            <div class="h-9 w-24 bg-neutral-200 dark:bg-white/10 rounded-xl shrink-0"></div>
        </div>`).join('');
}

function renderList(rooms) {
    if (!rooms.length) {
        els.list.innerHTML = emptyState();
        return;
    }

    els.list.innerHTML = rooms.map((room, i) => {
        const absent = currentAbsent(room);
        const isDone = absent !== null;
        const statusBadge = isDone
            ? `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-200/70 dark:ring-emerald-500/20">
                   <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                   អវត្តមាន ${absent}
               </span>`
            : `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full bg-neutral-100 text-neutral-500 dark:bg-white/5 dark:text-neutral-400 ring-1 ring-neutral-200/70 dark:ring-white/10">
                   <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007M12 21a9 9 0 100-18 9 9 0 000 18z" /></svg>
                   មិនទាន់បញ្ចូល
               </span>`;

        return `
            <div class="fade-up group flex items-center justify-between gap-4 p-4 sm:p-5 bg-white/85 dark:bg-neutral-900/70 backdrop-blur-sm border ${isDone ? 'border-emerald-200/70 dark:border-emerald-500/15' : 'border-neutral-200/80 dark:border-white/10'} rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-300/70 dark:hover:border-indigo-500/30 transition-all duration-300" style="animation-delay:${Math.min(i, 8) * 60}ms">
                <div class="flex items-center gap-3.5 min-w-0 flex-1">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white font-bold text-sm shadow-md shadow-indigo-500/20 shrink-0">
                        ${escapeHtml(room.room ?? '?')}
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-neutral-900 dark:text-white">បន្ទប់ ${escapeHtml(room.room ?? 'N/A')}</div>
                        <div class="text-xs text-neutral-400 dark:text-neutral-500 truncate">
                            ${escapeHtml(room.major ?? '')} &middot; ${escapeHtml(room.degree ?? '')} &middot; និស្សិត ${escapeHtml(room.student_total ?? 0)} នាក់
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    ${statusBadge}
                    <button data-id="${room.id}" class="enter-absence-btn px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl shadow-sm shadow-indigo-500/25 hover:bg-indigo-700 hover:shadow-md hover:shadow-indigo-500/30 active:scale-95 transition-all">
                        បញ្ចូល/កែប្រែ
                    </button>
                </div>
            </div>`;
    }).join('');
}

function byRoomAscending(a, b) {
    // Numeric-aware compare so "104" < "105" < "Hall 505", not alphabetical ("104" < "2xx").
    return String(a.room ?? '').localeCompare(String(b.room ?? ''), undefined, { numeric: true, sensitivity: 'base' });
}

async function fetchRooms(search = '') {
    const res = await fetch(`${API_BASE}?search=${encodeURIComponent(search)}`, {
        headers: { Accept: 'application/json' },
    });
    const json = await res.json();
    const rooms = Array.isArray(json.data) ? json.data : [];
    return rooms.sort(byRoomAscending);
}

async function loadRooms(search = '', { silent = false } = {}) {
    if (!silent) els.list.innerHTML = skeletonRows();
    currentRooms = await fetchRooms(search);
    renderList(currentRooms);
}

// ---------- Custom modal ----------

function clampToMax(value) {
    const max = activeRoom?.student_total ?? 0;
    return Math.min(Math.max(0, value), max);
}

function setModalError(message) {
    if (!els.error) return;
    els.error.textContent = message ?? '';
    els.error.classList.toggle('hidden', !message);
}

function openModal(room) {
    activeRoom = room;
    const studentTotal = room.student_total ?? 0;

    els.modalTitle.textContent = `បន្ទប់ ${room.room ?? ''}`;
    els.modalSubtitle.textContent = `${window.EXAM_ROUND_LABEL} — ${ROUND_TIMES[roundIndex]}`;
    els.maxHint.textContent = `អតិបរមា ${studentTotal} នាក់ (និស្សិតសរុបក្នុងបន្ទប់)`;
    els.input.value = currentAbsent(room) ?? 0;
    els.input.max = studentTotal;
    setModalError(null);

    els.modal.classList.remove('hidden');
    els.modal.classList.add('flex');
    requestAnimationFrame(() => {
        els.modalCard.classList.remove('scale-95', 'opacity-0');
        els.modalCard.classList.add('scale-100', 'opacity-100');
    });
    setTimeout(() => els.input?.focus(), 200);
}

function closeModal() {
    els.modalCard.classList.add('scale-95', 'opacity-0');
    els.modalCard.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        els.modal.classList.add('hidden');
        els.modal.classList.remove('flex');
        activeRoom = null;
    }, 250);
}

async function saveAbsence() {
    if (!activeRoom) return;

    const studentTotal = activeRoom.student_total ?? 0;
    const n = Number(els.input.value);

    if (els.input.value === '' || Number.isNaN(n) || n < 0) {
        setModalError('សូមបញ្ចូលលេខត្រឹមត្រូវ (Enter a valid number)');
        return;
    }
    if (n > studentTotal) {
        setModalError(`ចំនួនអវត្តមានមិនអាចលើសពី ${studentTotal} បានទេ`);
        return;
    }
    setModalError(null);

    els.saveBtn.disabled = true;
    els.saveBtn.classList.add('opacity-60');

    const absences = Array.isArray(activeRoom.absences) ? [...activeRoom.absences] : [];
    while (absences.length < 3) absences.push({ total: 0 });
    absences[roundIndex] = {
        major: activeRoom.major,
        time: ROUND_TIMES[roundIndex],
        total: n,
    };

    const res = await fetch(`${API_BASE}/${activeRoom.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({
            room: activeRoom.room,
            shift: activeRoom.shift,
            major: activeRoom.major,
            degree: activeRoom.degree,
            student_total: activeRoom.student_total,
            majors: activeRoom.majors,
            exam_date: activeRoom.exam_date,
            remark: activeRoom.remark,
            absences,
        }),
    });

    els.saveBtn.disabled = false;
    els.saveBtn.classList.remove('opacity-60');

    if (res.ok) {
        closeModal();
        showToast('success', 'រក្សាទុកបានជោគជ័យ! (Saved successfully)');
        loadRooms(els.search?.value || '');
    } else {
        const body = await res.json().catch(() => ({}));
        const firstError = body.errors ? Object.values(body.errors)[0]?.[0] : null;
        setModalError(firstError || body.message || 'មានបញ្ហាកើតឡើង (Something went wrong)');
    }
}

// ---------- Events ----------

els.search?.addEventListener('input', (e) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadRooms(e.target.value), 300);
});

els.list?.addEventListener('click', (e) => {
    const btn = e.target.closest('.enter-absence-btn');
    if (!btn) return;
    const room = currentRooms.find((r) => String(r.id) === String(btn.dataset.id));
    if (room) openModal(room);
});

els.minusBtn?.addEventListener('click', () => {
    els.input.value = clampToMax((Number(els.input.value) || 0) - 1);
});
els.plusBtn?.addEventListener('click', () => {
    els.input.value = clampToMax((Number(els.input.value) || 0) + 1);
});

els.saveBtn?.addEventListener('click', saveAbsence);
els.cancelBtn?.addEventListener('click', closeModal);
els.closeBtn?.addEventListener('click', closeModal);
els.modal?.addEventListener('click', (e) => {
    if (e.target === els.modal) closeModal();
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !els.modal.classList.contains('hidden')) closeModal();
});

loadRooms();

// ---------- Auto-refresh ----------
// Keep the list current for whoever's watching the screen, without disrupting
// anyone actively entering a number in the modal.
const AUTO_REFRESH_MS = 5 * 60 * 1000;

setInterval(() => {
    if (!els.modal.classList.contains('hidden')) return; // entry in progress — skip this tick
    loadRooms(els.search?.value || '', { silent: true });
}, AUTO_REFRESH_MS);
