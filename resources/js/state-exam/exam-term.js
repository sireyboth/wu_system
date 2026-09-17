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

function categoryName(c) {
    return (c.name_kh || c.name_en || '').trim();
}

/** Suggestions only (via <datalist>) — the category field itself is free text. */
function renderCategoryOptions(dom) {
    if (!dom.examTermCategoryList) return;
    dom.examTermCategoryList.innerHTML = state.examCategories
        .map((c) => `<option value="${categoryName(c).replace(/"/g, '&quot;')}"></option>`)
        .join('');
}

/** Matches typed text against a known category (by name, case-insensitive); creates one if there's no match. */
async function resolveCategoryId(dom, ApiService, typedName) {
    const existing = state.examCategories.find((c) => categoryName(c).toLowerCase() === typedName.toLowerCase());
    if (existing) return existing.id;

    const { error, data } = await ApiService.request(CONFIG.EXAM_CATEGORIES_API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name_en: typedName, name_kh: typedName }),
    });

    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || `Could not create category "${typedName}".` });
        return null;
    }

    const created = data?.data;
    if (created) {
        state.examCategories.push(created);
        renderCategoryOptions(dom);
    }
    return created?.id ?? null;
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

/**
 * Opens the modal. Pass an existing term to edit it (fields pre-filled,
 * submit does a PUT); omit it to create a new one (submit does a POST).
 */
export function openExamTermModal(dom, term = null) {
    dom.examTermForm?.reset();
    resetSlotRows(dom);
    state.editingTermId = term?.id ?? null;

    if (term) {
        if (dom.examTermModalTitle) dom.examTermModalTitle.textContent = 'កែប្រែការប្រឡង (Edit Exam Term)';
        if (dom.examTermSubmitBtn) dom.examTermSubmitBtn.textContent = 'រក្សាទុក (Save)';
        if (dom.examTermCategoryInput) dom.examTermCategoryInput.value = term.category ? categoryName(term.category) : '';
        if (dom.examTermCampusSelect) dom.examTermCampusSelect.value = term.campus_id ?? term.campus?.id ?? '';
        if (dom.examTermTitleInput) dom.examTermTitleInput.value = term.title ?? '';
        if (dom.examTermDateInput) dom.examTermDateInput.value = term.exam_date ?? '';
        if (dom.examTermActiveInput) dom.examTermActiveInput.checked = !!term.is_active;

        const slots = term.time_slots ?? [];
        if (slots.length && dom.examTermSlotsContainer) {
            dom.examTermSlotsContainer.innerHTML = '';
            slots.forEach((slot) => addSlotRow(dom, slot));
        }
    } else {
        if (dom.examTermModalTitle) dom.examTermModalTitle.textContent = 'បង្កើតការប្រឡងថ្មី (New Exam Term)';
        if (dom.examTermSubmitBtn) dom.examTermSubmitBtn.textContent = 'បង្កើត (Create)';
        if (dom.examTermActiveInput) dom.examTermActiveInput.checked = true;
    }

    openModal(dom.examTermModal, dom.examTermModalCard);
}

/** Opens the modal in edit mode for whichever term is currently selected in the term filter dropdown. */
export function openExamTermModalForSelected(dom) {
    const term = findTerm(dom.termFilterSelect?.value);
    if (!term) {
        Toast.fire({ icon: 'warning', title: 'Select an exam term above first.' });
        return;
    }
    openExamTermModal(dom, term);
}

export function closeExamTermModal(dom) {
    state.editingTermId = null;
    closeModal(dom.examTermModal, dom.examTermModalCard);
}

export async function submitExamTermForm(dom, ApiService, onDone) {
    if (!dom.examTermForm) return;

    const title = dom.examTermTitleInput?.value?.trim();
    const campusId = dom.examTermCampusSelect?.value;
    const categoryTyped = dom.examTermCategoryInput?.value?.trim();

    if (!title || !categoryTyped || !campusId) {
        Toast.fire({ icon: 'warning', title: 'Title, Category, and Campus are required.' });
        return;
    }

    if (dom.examTermSubmitBtn) dom.examTermSubmitBtn.disabled = true;

    const categoryId = await resolveCategoryId(dom, ApiService, categoryTyped);
    if (!categoryId) {
        if (dom.examTermSubmitBtn) dom.examTermSubmitBtn.disabled = false;
        return; // resolveCategoryId already showed a toast
    }

    const payload = {
        exam_category_id: categoryId,
        campus_id: campusId,
        title,
        exam_date: dom.examTermDateInput?.value || null,
        is_active: !!dom.examTermActiveInput?.checked,
        time_slots: collectSlots(dom),
    };

    const isEditing = !!state.editingTermId;
    const url = isEditing ? `${CONFIG.EXAM_TERMS_API}/${state.editingTermId}` : CONFIG.EXAM_TERMS_API;
    const method = isEditing ? 'PUT' : 'POST';

    const { error, data } = await ApiService.request(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
    if (dom.examTermSubmitBtn) dom.examTermSubmitBtn.disabled = false;

    if (error) {
        const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
        Toast.fire({ icon: 'error', title: firstError || data?.message || `Could not ${isEditing ? 'update' : 'create'} exam term.` });
        return;
    }

    Toast.fire({ icon: 'success', title: isEditing ? 'Exam term updated.' : 'Exam term created.' });
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

export function bindExamTermEvents(dom, ApiService) {
    dom.newExamTermBtn?.addEventListener('click', () => openExamTermModal(dom));
    dom.editExamTermBtn?.addEventListener('click', () => openExamTermModalForSelected(dom));
    dom.examTermForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        submitExamTermForm(dom, ApiService);
    });
    dom.addSlotBtn?.addEventListener('click', () => addSlotRow(dom));
    dom.examTermSlotsContainer?.addEventListener('click', (e) => {
        if (e.target.closest('.remove-slot-row')) e.target.closest('.flex')?.remove();
    });
    dom.roomExamTermSelect?.addEventListener('change', () => applySelectedTermSlots(dom));
}
