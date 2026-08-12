/**
 * Public invigilator duty-card lookup — no auth, no geofence.
 * Reuses the existing /api/v1/exam-states endpoint (already unauthenticated)
 * and flattens each room's `majors` rows (major, time, invigilator) into
 * one duty card per invigilator assignment.
 */

const API_BASE = '/api/v1/exam-states';

const els = {
    search: document.getElementById('invigilatorSearchInput'),
    list: document.getElementById('dutyCardList'),
};

let searchTimer = null;

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}

function formatDate(value) {
    if (!value) return 'មិនទាន់កំណត់ (TBD)';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

const NOT_ASSIGNED = 'មិនទាន់មានអ្នកឃ្លាំមើល (Not yet assigned)';

// ---------- Flatten rooms -> per-invigilator duty assignments ----------

function assignmentsFromRoom(room) {
    if (Array.isArray(room.majors) && room.majors.length > 0) {
        return room.majors.map((m) => ({
            invigilator: (m.invigilator || '').trim() || NOT_ASSIGNED,
            room: room.room,
            floorLabel: room.floor_label,
            major: m.major || room.major,
            time: m.time || '',
            shift: room.shift,
            examDate: room.exam_date,
            degree: room.degree,
        }));
    }

    // Legacy rows: a flat invigilators[] list with no per-major pairing.
    const names = Array.isArray(room.invigilators) ? room.invigilators.filter((n) => (n || '').trim()) : [];
    if (names.length > 0) {
        return names.map((name) => ({
            invigilator: name.trim(),
            room: room.room,
            floorLabel: room.floor_label,
            major: room.major,
            time: '',
            shift: room.shift,
            examDate: room.exam_date,
            degree: room.degree,
        }));
    }

    // No majors rows and no invigilators at all — still show the room itself.
    return [{
        invigilator: NOT_ASSIGNED,
        room: room.room,
        floorLabel: room.floor_label,
        major: room.major,
        time: '',
        shift: room.shift,
        examDate: room.exam_date,
        degree: room.degree,
    }];
}

function matchesSearch(assignment, keyword) {
    if (!keyword) return true;
    const haystack = [
        assignment.invigilator,
        assignment.room,
        assignment.floorLabel,
        assignment.major,
        assignment.degree,
        assignment.shift,
    ].join(' ').toLowerCase();
    return haystack.includes(keyword.toLowerCase());
}

// ---------- Rendering ----------

function emptyState() {
    return `
        <div class="col-span-full flex flex-col items-center justify-center py-16 text-center no-print">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-neutral-100 dark:bg-white/5 text-neutral-300 dark:text-neutral-600 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">រកមិនឃើញកាតព្វកិច្ចទេ</p>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">No duty assignment matches your search</p>
        </div>`;
}

function promptState() {
    return `
        <div class="col-span-full flex flex-col items-center justify-center py-16 text-center no-print">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-400 dark:text-indigo-300 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">សូមវាយឈ្មោះ ឬបន្ទប់ដើម្បីស្វែងរក</p>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Type your name or a room number above to find your duty card</p>
        </div>`;
}

function skeletonCards(n = 2) {
    return Array.from({ length: n }).map((_, i) => `
        <div class="rounded-3xl border border-neutral-200/70 dark:border-white/5 bg-white/60 dark:bg-neutral-900/50 h-52 animate-pulse" style="animation-delay:${i * 80}ms"></div>`
    ).join('');
}

function dutyCard(a, i) {
    return `
        <div class="fade-up group relative overflow-hidden bg-white/90 dark:bg-neutral-900/80 backdrop-blur-sm border border-neutral-200/80 dark:border-white/10 rounded-3xl shadow-sm hover:shadow-xl hover:border-indigo-300/70 dark:hover:border-indigo-500/30 transition-all duration-500" style="animation-delay:${Math.min(i, 8) * 70}ms">

            <!-- Card header -->
            <div class="relative px-6 pt-6 pb-5 bg-gradient-to-br from-indigo-600 to-indigo-800 text-white">
                <span class="pointer-events-none absolute -top-4 -right-3 font-display text-6xl font-bold text-white/[0.08] select-none">
                    ${escapeHtml(a.room ?? '?')}
                </span>
                <div class="relative">
                    <div class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-200">Invigilator Duty Card</div>
                    <div class="mt-1 text-lg font-bold leading-tight truncate ${a.invigilator === NOT_ASSIGNED ? 'italic text-indigo-200' : ''}">${escapeHtml(a.invigilator)}</div>
                </div>
            </div>

            <!-- Card body -->
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">បន្ទប់ (Room)</div>
                        <div class="mt-1 text-xl font-black text-neutral-900 dark:text-white">${escapeHtml(a.room ?? 'N/A')}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">ជាន់ (Floor)</div>
                        <div class="mt-1 text-xl font-black text-neutral-900 dark:text-white">${escapeHtml(a.floorLabel ?? 'N/A')}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-neutral-100 dark:border-white/5">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">ម៉ោង (Time)</div>
                        <div class="mt-1 text-sm font-bold text-neutral-700 dark:text-neutral-200">${escapeHtml(a.time || a.shift || 'TBD')}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">ថ្ងៃប្រឡង (Date)</div>
                        <div class="mt-1 text-sm font-bold text-neutral-700 dark:text-neutral-200">${escapeHtml(formatDate(a.examDate))}</div>
                    </div>
                </div>

                <div class="pt-4 border-t border-neutral-100 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">ជំនាញ / សញ្ញាបត្រ (Major / Degree)</div>
                    <div class="mt-1 text-sm font-semibold text-neutral-700 dark:text-neutral-200">${escapeHtml(a.major ?? '')}${a.degree ? ' &middot; ' + escapeHtml(a.degree) : ''}</div>
                </div>
            </div>

            <!-- Card footer -->
            <div class="px-6 py-4 bg-neutral-50 dark:bg-white/[0.03] border-t border-neutral-100 dark:border-white/5 flex items-center justify-between gap-3">
                <p class="text-[11px] text-neutral-400 dark:text-neutral-500 leading-snug">
                    សូមមកដល់មុនម៉ោង ១៥ នាទី<br>
                    <span class="text-[10px]">Please arrive 15 min early</span>
                </p>
                <button data-print class="no-print shrink-0 px-3.5 py-2 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-xl transition-colors">
                    បោះពុម្ព (Print)
                </button>
            </div>
        </div>`;
}

function renderCards(assignments) {
    if (!assignments.length) {
        els.list.innerHTML = emptyState();
        return;
    }
    els.list.innerHTML = assignments.map(dutyCard).join('');
}

// ---------- Data flow ----------

async function fetchRooms(search = '') {
    const res = await fetch(`${API_BASE}?search=${encodeURIComponent(search)}`, {
        headers: { Accept: 'application/json' },
    });
    const json = await res.json();
    return Array.isArray(json.data) ? json.data : [];
}

async function runSearch(keyword) {
    const trimmed = keyword.trim();
    if (!trimmed) {
        els.list.innerHTML = promptState();
        return;
    }

    els.list.innerHTML = skeletonCards();

    const rooms = await fetchRooms(trimmed);
    const assignments = rooms
        .flatMap(assignmentsFromRoom)
        .filter((a) => matchesSearch(a, trimmed));

    renderCards(assignments);
}

els.search?.addEventListener('input', (e) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => runSearch(e.target.value), 300);
});

els.list?.addEventListener('click', (e) => {
    if (e.target.closest('[data-print]')) window.print();
});

els.list.innerHTML = promptState();
