/**
 * Pure, side-effect-light helpers for reading/writing form state.
 * No API calls, no global state — easy to unit test in isolation.
 */

/**
 * Populate a <select> element with options, preserving its placeholder (option[0]).
 */
export function fillSelectOptions(element, items, valueField = 'id', textField = 'name') {
    if (!element) return;
    const placeholder = element.options[0];
    element.innerHTML = '';
    if (placeholder) element.appendChild(placeholder);

    items.forEach((item) => {
        const opt = document.createElement('option');

        if (typeof item === 'string') {
            opt.value = item;
            opt.textContent = item;
        } else {
            // CRITICAL: always pass the numeric primary key ID for all fields
            const recordId = item[valueField] ?? item.id;
            opt.value = recordId;
            opt.setAttribute('data-id', recordId);
            opt.textContent = item.name_kh || item.name || item[textField];
        }
        element.appendChild(opt);
    });
}

/**
 * Parses bracketed form field names like "guardians[0][addresses][0][street]"
 * into a clean nested JS object/array tree.
 */
export function parseNestedFormData(formElement) {
    const formData = new FormData(formElement);
    const root = {};

    for (const [key, value] of formData.entries()) {
        if (key === 'search') continue;
        const cleanVal = value.toString().trim();
        const finalVal = cleanVal === '' ? null : cleanVal;

        const parts = key.split(/\]\[|\[|\]/).filter((p) => p !== '');

        let current = root;
        for (let i = 0; i < parts.length; i++) {
            const part = parts[i];
            const isLast = i === parts.length - 1;
            const nextPart = parts[i + 1];
            const isNextAnIndex = nextPart !== undefined && !isNaN(parseInt(nextPart, 10));

            if (isLast) {
                current[part] = finalVal;
            } else {
                if (!current[part]) {
                    current[part] = isNextAnIndex ? [] : {};
                }
                current = current[part];
            }
        }
    }
    return root;
}

// Mirrors Term::resolveDefault() on the backend — picks whichever active
// term's date range covers today, falling back to the most recently
// started active term. This is only ever a *default selection*; the
// backend re-resolves independently if the field is left blank, so this
// just needs to be a sensible starting point, not the source of truth.
function pickDefaultTerm(terms) {
    const today = new Date().toISOString().slice(0, 10);
    const active = terms.filter((t) => t.is_active);
    const current = active.find((t) => t.start_date <= today && t.end_date >= today);
    if (current) return current;
    const started = active.filter((t) => t.start_date <= today).sort((a, b) => b.start_date.localeCompare(a.start_date));
    if (started.length) return started[0];
    const byStart = [...active].sort((a, b) => b.start_date.localeCompare(a.start_date));
    return byStart[0] ?? null;
}

/**
 * Fills a term <select> with every term, active or not, pre-selecting a
 * sensible default (see pickDefaultTerm) — used by the single and bulk
 * "Advance Semester" forms so different batches can be filed under
 * different terms without flipping a global "active" flag back and forth.
 */
export async function loadTermOptions(ApiService, element) {
    if (!element) return;

    const { error, data } = await ApiService.request('/api/v1/terms?per_page=1000');
    if (error) {
        element.innerHTML = '<option value="">Could not load terms</option>';
        return;
    }

    const terms = data?.data ?? data ?? [];
    const defaultTerm = pickDefaultTerm(terms);

    element.innerHTML = terms.map((t) => `
        <option value="${t.id}" ${defaultTerm && t.id === defaultTerm.id ? 'selected' : ''}>
            ${t.code} — ${t.name ?? t.full_name ?? ''}${t.is_active ? ' (active)' : ''}
        </option>`).join('');
}

/**
 * Escapes HTML special characters. Wrap any user-supplied string with this
 * before interpolating into innerHTML (table rows, preview panel, etc.)
 * to avoid stored/reflected XSS from names, addresses, codes, etc.
 */
export function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}
