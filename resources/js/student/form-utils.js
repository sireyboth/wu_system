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
