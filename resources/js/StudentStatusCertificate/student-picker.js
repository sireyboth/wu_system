/**
 * @file student-picker.js
 * @description Student picker modal for provisional certificate generation.
 *
 * Import in resources/js/probisional/index.js:
 *   import './student-picker.js';
 */

// ─── Config ───────────────────────────────────────────────────────────────────

const CONFIG = {
    API_URL:         '/api/v1/students',
    CERT_ROUTE:      '/provisional-certificate/create',
    SEARCH_DELAY_MS: 300,
    MODAL_ANIM_MS:   300,
    DEBUG:           false,
};

// ─── Helpers ──────────────────────────────────────────────────────────────────

const log = (...a) => CONFIG.DEBUG && console.log('[StudentPicker]', ...a);

function debounce(fn, ms) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
}

function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric',
    });
}

// ─── State ────────────────────────────────────────────────────────────────────

const state = {
    /** @type {{ id: string, nameKh: string, nameEn: string }|null} */
    selected: null,
};

// ─── DOM refs (lazy) ──────────────────────────────────────────────────────────

const El = {
    modal:     () => document.getElementById('studentPickerModal'),
    card:      () => document.getElementById('pickerModalCard'),
    search:    () => document.getElementById('pickerSearchInput'),
    loader:    () => document.getElementById('pickerLoader'),
    tbody:     () => document.getElementById('picker-table-body'),
    preview:   () => document.getElementById('pickerSelectionPreview'),
    beginBtn:  () => document.getElementById('pickerBeginBtn'),
    closeBtn:  () => document.getElementById('pickerCloseBtn'),
    cancelBtn: () => document.getElementById('pickerCancelBtn'),
    openBtn:   () => document.getElementById('openPickerBtn'),
};

// ─── API ──────────────────────────────────────────────────────────────────────

async function fetchStudents(search = '') {
    const url = `${CONFIG.API_URL}?search=${encodeURIComponent(search)}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error(`GET ${url} → ${res.status}`);
    
    const json = await res.json();
    
    // ─── ADD LOGS HERE ───
    console.log('%c[API Raw Response]', 'color: #10b981; font-weight: bold;', json);
    
    return Array.isArray(json.data) ? json.data
         : Array.isArray(json)      ? json
         : [];
}

// ─── Rendering ────────────────────────────────────────────────────────────────

function renderTable(students) {
    const tbody = El.tbody();
    if (!tbody) return;

    if (!students?.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-neutral-400 dark:text-neutral-500">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-8 h-8 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                     M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0
                                     0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm">រកមិនឃើញសិស្ស</span>
                    </div>
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = students.map(buildRow).join('');
}

function buildRow(s, i) {
    
    const isFemale = ['F', 'Female', 'ស្រី'].includes(s.sex);
    const sexBadge = isFemale
        ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        bg-pink-50 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400">ស្រី</span>`
        : `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">ប្រុស</span>`;

    return `
        <tr class="picker-row cursor-pointer select-none
                   hover:bg-indigo-50/60 dark:hover:bg-indigo-500/5
                   transition-colors duration-150"
            data-id="${s.id}"
            data-name-kh="${s.person.first_name_kh ?? ''}"
            data-name-en="${s.name_en ?? ''}">
            <td class="px-4 py-3 text-neutral-400 font-mono text-xs">${i + 1}</td>
            <td class="px-4 py-3">
                <div class="font-semibold text-neutral-900 dark:text-white leading-snug">${s.name_kh ?? '—'}</div>
                <div class="text-xs text-neutral-400 font-mono mt-0.5">${s.name_en ?? ''}</div>
            </td>
            <td class="px-4 py-3">${sexBadge}</td>
            <td class="px-4 py-3 text-xs text-neutral-500 font-mono whitespace-nowrap">${formatDate(s.dob)}</td>
            <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400">
                ${s.student_id ?? s.code ?? '—'}
            </td>
            <td class="px-4 py-3 text-sm text-neutral-700 dark:text-neutral-300">
                ${s.major ?? s.department ?? '—'}
            </td>
            <td class="px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400">
                ${s.batch ?? s.generation ?? '—'}
            </td>
        </tr>`;
}

// ─── Selection ────────────────────────────────────────────────────────────────

function selectRow(row) {
    El.tbody()?.querySelectorAll('.picker-row.is-selected').forEach(r => {
        r.classList.remove(
            'is-selected', 'bg-indigo-50', 'dark:bg-indigo-500/10',
            'ring-1', 'ring-inset', 'ring-indigo-300', 'dark:ring-indigo-500/40',
        );
    });

    row.classList.add(
        'is-selected', 'bg-indigo-50', 'dark:bg-indigo-500/10',
        'ring-1', 'ring-inset', 'ring-indigo-300', 'dark:ring-indigo-500/40',
    );

    state.selected = {
        id:     row.dataset.id,
        nameKh: row.dataset.nameKh,
        nameEn: row.dataset.nameEn,
    };

    const preview = El.preview();
    if (preview) {
        preview.innerHTML = `
            <span class="text-neutral-900 dark:text-white font-semibold">${state.selected.nameKh}</span>
            <span class="text-neutral-400 mx-1">·</span>
            <span class="font-mono text-xs text-indigo-500">${state.selected.nameEn}</span>
            <span class="ml-2 text-xs text-neutral-400">ត្រូវបានជ្រើស</span>`;
    }

    const btn = El.beginBtn();
    if (btn) btn.disabled = false;

    log('Selected:', state.selected);
}

function clearSelection() {
    state.selected = null;

    const preview = El.preview();
    if (preview) {
        preview.innerHTML = '<span class="italic text-neutral-400">គ្មានសិស្សត្រូវបានជ្រើសរើស</span>';
    }

    const btn = El.beginBtn();
    if (btn) btn.disabled = true;
}

// ─── Data loading ─────────────────────────────────────────────────────────────

async function load(search = '') {
    El.loader()?.classList.remove('hidden');

    try {
        const students = await fetchStudents(search);
        log('Loaded', students.length, 'students');
        renderTable(students);
        clearSelection();
    } catch (err) {
        log('Load error:', err);
        const tbody = El.tbody();
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-rose-500 text-sm">
                        មិនអាចទាញទិន្នន័យ — ${err.message}
                    </td>
                </tr>`;
        }
    } finally {
        El.loader()?.classList.add('hidden');
    }
}

// ─── Modal open / close ───────────────────────────────────────────────────────

function openModal() {
    const modal = El.modal();
    const card  = El.card();
    if (!modal || !card) {
        console.error('[StudentPicker] Modal elements not found. Check @include is in your Blade.');
        return;
    }

    // Remove visibility:hidden so element enters the paint tree
    modal.classList.remove('invisible');

    // Double rAF: frame 1 = layout change registered, frame 2 = transition fires
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        });
    });

    // Reset & fetch fresh every open
    const search = El.search();
    if (search) search.value = '';
    clearSelection();
    load();

    setTimeout(() => El.search()?.focus(), CONFIG.MODAL_ANIM_MS);
}

function closeModal() {
    const modal = El.modal();
    const card  = El.card();
    if (!modal || !card) return;

    modal.classList.add('opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');

    setTimeout(() => modal.classList.add('invisible'), CONFIG.MODAL_ANIM_MS);
}

// ─── Begin ────────────────────────────────────────────────────────────────────

function beginCertificate() {
    if (!state.selected) return;
    const url = `${CONFIG.CERT_ROUTE}/${state.selected.id}`;
    log('Navigating to:', url);
    window.location.href = url;
}

// ─── Event binding ────────────────────────────────────────────────────────────

function bindEvents() {
    // "Create New Certificate" button — no onclick in Blade needed
    El.openBtn()?.addEventListener('click', () => openModal());

    // Search input — debounced
    El.search()?.addEventListener(
        'input',
        debounce((e) => load(e.target.value), CONFIG.SEARCH_DELAY_MS),
    );

    // Row click — event delegation
    El.tbody()?.addEventListener('click', (e) => {
        const row = e.target.closest('.picker-row');
        if (row) selectRow(row);
    });

    // Close triggers
    El.closeBtn()?.addEventListener('click', closeModal);
    El.cancelBtn()?.addEventListener('click', closeModal);

    // Backdrop click
    El.modal()?.addEventListener('click', (e) => {
        if (e.target === El.modal()) closeModal();
    });

    // Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !El.modal()?.classList.contains('invisible')) {
            closeModal();
        }
    });

    // Begin button
    El.beginBtn()?.addEventListener('click', beginCertificate);
}

// ─── Public API ───────────────────────────────────────────────────────────────
// Still exposed on window in case other Blade files need to trigger it

window.AppModal = {
    toggle(open) {
        const isOpen = !El.modal()?.classList.contains('invisible');
        const target = (open === undefined) ? !isOpen : Boolean(open);
        target ? openModal() : closeModal();
    },
};

// ─── Init ─────────────────────────────────────────────────────────────────────
// Vite defers module execution, so DOMContentLoaded may have already fired.

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindEvents);
} else {
    bindEvents();
}

