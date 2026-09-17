/**
 * Exam Terms — one real exam event (a date, campus, category, and its own
 * list of real time slots). Every exam room belongs to one; a term's
 * time slots drive the "time" dropdown on each majors-breakdown row in
 * the room modal, replacing what used to be one hardcoded 4-option list
 * shared by every exam program.
 *
 * Deliberately no dedicated page/sidebar link — same pattern as
 * RetakeTerm: managed as a small "+" button and modal right on the page
 * that actually uses it (the Exam Rooms table), since a term only
 * matters in the context of the rooms it's grouping.
 */
import { CONFIG } from './config.js';
import { state, Toast } from './core.js';
import { setTimeSlots, refreshTimeDropdowns } from './stateExam-majors.js';

function openModal(modalEl, cardEl) {
    if (!modalEl) return;
    modalEl.classList.remove('invisible', 'opacity-0');
    modalEl.classList.add('flex');
    requestAnimationFrame(() => {
        cardEl?.classList.remove('scale-90', 'opacity-0');
        cardEl?.classList.add('scale-100', 'opacity-100');
    });
}

function closeModal(modalEl, cardEl) {
    if (!modalEl) return;
    modalEl.classList.add('opacity-0');
    cardEl?.classList.remove('scale-100', 'opacity-100');
    cardEl?.classList.add('scale-90', 'opacity-0');
    setTimeout(() => {
        modalEl.classList.add('invisible');
        modalEl.classList.remove('flex');
    }, 300);
}

function findTerm(id) {
    return state.examTerms.find((t) => String(t.id) === String(id));
}

/** Populates the term filter (table) and the room modal's term select from state.examTerms. */
function renderTermSelects(dom) {
    const options = state.examTerms.map((t) => {
        const label = `${t.title}${t.category?.name_en ? ` — ${t.category.name_en}` : ''}${t.is_active ? '' : ' (inactive)'}`;
        return `<option value="${t.id}">${label}</option>`;
    }).join('');

    if (dom.termFilterSelect) {
        dom.termFilterSelect.innerHTML = `<option value="">គ្រប់រយៈពេល (All terms)</option>${options}`;
    }
    if (dom.roomExamTermSelect) {
        dom.roomExamTermSelect.innerHTML = `<option value="" disabled selected>-- Select Exam Term --</option>${options}`;
    }
}

function renderCategoryOptions(dom) {
    if (!dom.examTermCategorySelect) return;
    const current = dom.examTermCategorySelect.value;
    dom.examTermCategorySelect.innerHTML = state.examCategories
        .map((c) => `<option value="${c.id}">${c.name_kh || c.name_en}${c.name_kh && c.name_en ? ` (${c.name_en})` : ''}</option>`)
        .join('');
    if (current) dom.examTermCategorySelect.value = current;
}

export async function loadExamTermLookups(dom, ApiService) {
    const [termsRes, categoriesRes, campusesRes] = await Promise.all([
        ApiService.request(`${CONFIG.EXAM_TERMS_API}?per_page=200`),
        ApiService.request(`${CONFIG.EXAM_CATEGORIES_API}?per_page=200`),
        ApiService.request(`${CONFIG.CAMPUSES_API ?? '/api/v1/campuses'}?per_page=200`),
    ]);

    state.examTerms = termsRes.error ? [] : (termsRes.data?.data ?? termsRes.data ?? []);
    state.examCategories = categoriesRes.error ? [] : (categoriesRes.data?.data ?? categoriesRes.data ?? []);
    renderTermSelects(dom);
    renderCategoryOptions(dom);

    if (dom.examTermCampusSelect && !campusesRes.error) {
        const campuses = campusesRes.data?.data ?? campusesRes.data ?? [];
        dom.examTermCampusSelect.innerHTML = campuses
            .map((c) => `<option value="${c.id}">${c.name_kh || c.name_en}</option>`)
            .join('');
    }
}

/** Applies the currently-selected room-modal Exam Term's time slots to the majors rows' dropdowns. */
export function applySelectedTermSlots(dom) {
    const term = findTerm(dom.roomExamTermSelect?.value);
    setTimeSlots(term?.time_slots ?? []);
    refreshTimeDropdowns(dom);
}

function addSlotRow(dom, value = '') {
    if (!dom.examTermSlotsContainer) return;
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2';
    row.innerHTML = `
        <input type="text" value="${value}" placeholder="e.g. 7:30-9:00" class="slot-input flex-1 text-sm p-2.5 rounded-xl border border-neutral-200 dark:border-white/10 bg-white dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <button type="button" class="remove-slot-row shrink-0 p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg">
            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    dom.examTermSlotsContainer.appendChild(row);
}

function resetSlotRows(dom) {
    if (!dom.examTermSlotsContainer) return;
    dom.examTermSlotsContainer.innerHTML = '';
    addSlotRow(dom);
}

function collectSlots(dom) {
    return Array.from(dom.examTermSlotsContainer?.querySelectorAll('.slot-input') ?? [])
        .map((input) => input.value.trim())
        .filter(Boolean);
}

export function openExamTermModal(dom) {
    dom.examTermForm?.reset();
    resetSlotRows(dom);
    if (dom.examTermActiveInput) dom.examTermActiveInput.checked = true;
    openModal(dom.examTermModal, dom.examTermModalCard);
}

export function closeExamTermModal(dom) {
    closeModal(dom.examTermModal, dom.examTermModalCard);
}

export async function submitExamTermForm(dom, ApiService, onDone) {
    if (!dom.examTermForm) return;

    const payload = {
        exam_category_id: dom.examTermCategorySelect?.value,
        campus_id: dom.examTermCampusSelect?.value,
        title: dom.examTermTitleInput?.value?.trim(),
        exam_date: dom.examTermDateInput?.value || null,
        is_active: !!dom.examTermActiveInput?.checked,
        time_slots: collectSlots(dom),
    };

    if (!payload.title || !payload.exam_category_id || !payload.campus_id) {
        Toast.fire({ icon: 'warning', title: 'Title, Category, and Campus are required.' });
        return;
    }

    if (dom.examTermSubmitBtn) dom.examTermSubmitBtn.disabled = true;
    const { error, data } = await ApiService.request(CONFIG.EXAM_TERMS_API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
    if (dom.examTermSubmitBtn) dom.examTermSubmitBtn.disabled = false;

    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || 'Could not create exam term.' });
        return;
    }

    Toast.fire({ icon: 'success', title: 'Exam term created.' });
    closeExamTermModal(dom);
    await loadExamTermLookups(dom, ApiService);
    // Pre-select the freshly created term on the room modal, since that's
    // almost always what the admin wants to do next.
    const created = data?.data;
    if (created && dom.roomExamTermSelect) {
        dom.roomExamTermSelect.value = created.id;
        applySelectedTermSlots(dom);
    }
    onDone?.();
}

/** Quick inline "+ add a category" — a prompt, not a full page, since categories are a short, rarely-changed list. */
export async function addCategoryInline(dom, ApiService) {
    const { value: nameEn } = await Swal.fire({
        title: 'New Exam Category',
        input: 'text',
        inputLabel: 'e.g. "Scholarship Exam", "Entrance Exam"',
        showCancelButton: true,
        confirmButtonText: 'Add',
    });
    if (!nameEn) return;

    const { error, data } = await ApiService.request(CONFIG.EXAM_CATEGORIES_API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name_en: nameEn, name_kh: nameEn }),
    });

    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || 'Could not add category.' });
        return;
    }

    Toast.fire({ icon: 'success', title: 'Category added.' });
    const categoriesRes = await ApiService.request(`${CONFIG.EXAM_CATEGORIES_API}?per_page=200`);
    state.examCategories = categoriesRes.error ? state.examCategories : (categoriesRes.data?.data ?? categoriesRes.data ?? []);
    renderCategoryOptions(dom);
    if (data?.data?.id && dom.examTermCategorySelect) dom.examTermCategorySelect.value = data.data.id;
}

export function bindExamTermEvents(dom, ApiService) {
    dom.newExamTermBtn?.addEventListener('click', () => openExamTermModal(dom));
    dom.examTermForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        submitExamTermForm(dom, ApiService);
    });
    dom.addSlotBtn?.addEventListener('click', () => addSlotRow(dom));
    dom.examTermSlotsContainer?.addEventListener('click', (e) => {
        if (e.target.closest('.remove-slot-row')) e.target.closest('.flex')?.remove();
    });
    dom.addCategoryBtn?.addEventListener('click', () => addCategoryInline(dom, ApiService));
    dom.roomExamTermSelect?.addEventListener('change', () => applySelectedTermSlots(dom));
}
