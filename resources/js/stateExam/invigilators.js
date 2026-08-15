/**
 * Public invigilator duty-card lookup — no auth, no geofence.
 * Reuses the existing /api/v1/exam-states endpoint (already unauthenticated).
 * Renders one "boarding pass"-style duty ticket per ROOM, listing every
 * major/invigilator assigned to it — so searching your own name still shows
 * the full room roster, not just your own row.
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
    if (!value) return 'TBD';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function seatCode(i) {
    // A1, A2, ... A9, B1, B2, ... boarding-pass style seat tag, purely decorative.
    const row = String.fromCharCode(65 + Math.floor(i / 9));
    return `${row}${(i % 9) + 1}`;
}

const NOT_ASSIGNED = 'មិនទាន់មានអនុរក្ស (Not yet assigned)';

// ---------- Normalize a room's majors/invigilators into one roster ----------

function rosterFromRoom(room) {
    if (Array.isArray(room.majors) && room.majors.length > 0) {
        return room.majors.map((m) => ({
            invigilator: (m.invigilator || '').trim() || NOT_ASSIGNED,
            major: m.major || room.major,
            time: m.time || '',
        }));
    }

    // Legacy rows: a flat invigilators[] list with no per-major pairing.
    const names = Array.isArray(room.invigilators) ? room.invigilators.filter((n) => (n || '').trim()) : [];
    if (names.length > 0) {
        return names.map((name) => ({ invigilator: name.trim(), major: room.major, time: '' }));
    }

    return [{ invigilator: NOT_ASSIGNED, major: room.major, time: '' }];
}

function matchesSearch(room, roster, keyword) {
    if (!keyword) return true;
    const haystack = [
        room.room,
        room.floor_label,
        room.major,
        room.degree,
        room.shift,
        ...roster.map((r) => `${r.invigilator} ${r.major}`),
    ].join(' ').toLowerCase();
    return haystack.includes(keyword.toLowerCase());
}

// ---------- Rendering ----------

function emptyState() {
    return `
        <div class="flex flex-col items-center justify-center py-16 text-center no-print">
            <div class="flex items-center justify-center w-14 h-14 rounded-full border border-neutral-200 dark:border-white/10 text-neutral-300 dark:text-neutral-600 mb-4">
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
        <div class="flex flex-col items-center justify-center py-16 text-center no-print">
            <div class="flex items-center justify-center w-14 h-14 rounded-full border border-indigo-200 dark:border-indigo-500/30 text-indigo-400 dark:text-indigo-300 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">សូមវាយឈ្មោះ ឬបន្ទប់ដើម្បីស្វែងរក</p>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Type your name or a room number above to find your duty pass</p>
        </div>`;
}

function skeletonCards(n = 1) {
    return Array.from({ length: n }).map((_, i) => `
        <div class="flex flex-col sm:flex-row rounded-2xl overflow-hidden border border-neutral-200 dark:border-white/10 shadow-sm animate-pulse" style="animation-delay:${i * 80}ms">
            <div class="flex-1 bg-white/70 dark:bg-neutral-900/60 h-56 sm:h-48"></div>
            <div class="w-full sm:w-36 bg-neutral-200/70 dark:bg-white/5 h-14 sm:h-48"></div>
        </div>`
    ).join('<div class="h-6 no-print"></div>');
}

function rosterRow(entry, i, highlight) {
    const isUnassigned = entry.invigilator === NOT_ASSIGNED;
    return `
        <div class="flex items-center gap-3 py-2.5 ${highlight ? '-mx-3 px-3 rounded-lg bg-amber-50 dark:bg-amber-500/10 ring-1 ring-amber-200/70 dark:ring-amber-500/20' : ''}">
            <span class="shrink-0 inline-flex items-center justify-center w-9 h-7 rounded-md bg-neutral-100 dark:bg-white/5 text-[11px] font-mono font-bold text-neutral-500 dark:text-neutral-400">${seatCode(i)}</span>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-bold truncate ${isUnassigned ? 'italic font-normal text-neutral-400 dark:text-neutral-500' : 'text-neutral-900 dark:text-white'}">${escapeHtml(entry.invigilator)}</div>
            </div>
            <div class="shrink-0 text-right">
                <div class="text-xs font-semibold text-neutral-600 dark:text-neutral-300">${escapeHtml(entry.major || '—')}</div>
                <div class="text-[10px] text-neutral-400 dark:text-neutral-500 font-mono">${escapeHtml(entry.time || '—')}</div>
            </div>
            ${highlight ? '<span class="shrink-0 px-1.5 py-0.5 rounded text-[9px] font-black tracking-wide bg-amber-400 text-amber-950">YOU</span>' : ''}
        </div>`;
}

function boardingPass(room, roster, i, keyword) {
    const kw = keyword.toLowerCase();

    return `
        <div class="fade-up flex flex-col sm:flex-row rounded-2xl overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200/80 dark:border-white/10 shadow-lg shadow-neutral-900/5" style="animation-delay:${Math.min(i, 8) * 70}ms">

            <!-- Main stub -->
            <div class="flex-1 min-w-0 p-6 sm:p-7">
                <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25c-2.485 0-4.5 2.015-4.5 4.5 0 1.591.827 2.99 2.077 3.79A6.75 6.75 0 004.5 17.25v.75a.75.75 0 00.75.75h13.5a.75.75 0 00.75-.75v-.75a6.75 6.75 0 00-5.077-6.71c1.25-.8 2.077-2.199 2.077-3.79 0-2.485-2.015-4.5-4.5-4.5z" />
                    </svg>
                    State Exam &middot; Invigilator Duty Pass
                </div>

                <div class="mt-4 flex items-end justify-between gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Room</div>
                        <div class="text-5xl sm:text-6xl font-black leading-none tracking-tight text-neutral-900 dark:text-white">${escapeHtml(room.room ?? '—')}</div>
                    </div>
                    <div class="text-right shrink-0 pb-1">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Floor</div>
                        <div class="text-2xl font-bold text-neutral-700 dark:text-neutral-200">${escapeHtml(room.floor_label ?? 'N/A')}</div>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-semibold text-neutral-500 dark:text-neutral-400">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        ${escapeHtml(formatDate(room.exam_date))}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347M4.26 10.147a60.436 60.436 0 018.232-4.41 60.436 60.436 0 018.232 4.41" /></svg>
                        ${escapeHtml(room.degree ?? 'N/A')}
                    </span>
                </div>

                <div class="relative my-5">
                    <div class="border-t-2 border-dashed border-neutral-200 dark:border-white/10"></div>
                </div>

                <div class="flex items-center justify-between mb-1">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Assigned Invigilators</div>
                    <div class="text-[10px] font-bold text-neutral-400 dark:text-neutral-500">${roster.length} ${roster.length === 1 ? 'seat' : 'seats'}</div>
                </div>
                <div class="divide-y divide-neutral-100 dark:divide-white/5">
                    ${roster.map((entry, idx) => rosterRow(entry, idx, kw && entry.invigilator.toLowerCase().includes(kw))).join('')}
                </div>
            </div>

            <!-- Perforated divider -->
            <div class="relative shrink-0 flex sm:flex-col items-center justify-center px-2 sm:px-0 sm:py-2 no-print">
                <div class="hidden sm:block w-px flex-1 border-l-2 border-dashed border-neutral-200 dark:border-white/10"></div>
                <div class="sm:hidden h-px w-full border-t-2 border-dashed border-neutral-200 dark:border-white/10"></div>
            </div>

            <!-- Stub -->
            <div class="shrink-0 w-full sm:w-36 bg-gradient-to-br from-indigo-600 to-indigo-800 text-white p-5 flex sm:flex-col items-center justify-between sm:justify-center gap-4">
                <div class="sm:text-center">
                    <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-indigo-200">Duty Pass</div>
                    <div class="text-xl font-black tracking-tight">${escapeHtml(room.room ?? '—')}</div>
                </div>

                <div class="flex sm:flex-col items-center gap-0.5" aria-hidden="true">
                    ${Array.from({ length: 10 }).map((_, b) => {
                        const cls = b % 3 === 0
                            ? 'block w-1 h-4 sm:w-4 sm:h-1 bg-white/70 rounded-full'
                            : 'block w-0.5 h-4 sm:w-4 sm:h-0.5 bg-white/70 rounded-full';
                        return `<span class="${cls}"></span>`;
                    }).join('')}
                </div>
            </div>
        </div>`;
}

function renderCards(rooms, keyword) {
    if (!rooms.length) {
        els.list.innerHTML = emptyState();
        return;
    }
    els.list.innerHTML = rooms.map(({ room, roster }, i) => boardingPass(room, roster, i, keyword)).join('<div class="h-6 no-print"></div>');
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
    const matches = rooms
        .map((room) => ({ room, roster: rosterFromRoom(room) }))
        .filter(({ room, roster }) => matchesSearch(room, roster, trimmed));

    renderCards(matches, trimmed);
}

els.search?.addEventListener('input', (e) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => runSearch(e.target.value), 300);
});

els.list.innerHTML = promptState();
