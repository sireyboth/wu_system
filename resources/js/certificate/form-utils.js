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
