/**
 * Shared, framework-agnostic helpers used by both staticTable.js and remoteTable.js.
 * These only ever operate on plain JS arrays/objects — no Alpine, no axios, no Blade.
 * Keeping them pure makes them trivially unit-testable on their own.
 */

/**
 * Read a value from a row, supporting dot-notation for nested/relation fields.
 * e.g. getValue(row, 'organization.name') -> row.organization.name
 */
export function getValue(row, key) {
    if (!key) return undefined;
    return key
        .split(".")
        .reduce((val, k) => (val == null ? undefined : val[k]), row);
}

/**
 * Format an ISO date/datetime string (e.g. "2026-08-10T08:04:06.000000Z")
 * into a short, human-readable form. Purely client-side — no Carbon/PHP
 * involved, since API-sourced rows never pass through Blade formatting.
 *
 * `format` accepts a couple of convenience shorthands, or falls through
 * to Intl.DateTimeFormat options for anything more specific.
 */
export function formatDate(value, format = "j M, Y") {
    if (!value) return "—";
    const date = new Date(value);
    if (isNaN(date)) return value; // not parseable — show raw rather than "Invalid Date"

    const months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ];

    switch (format) {
        case "j M, Y": // "10 Aug, 2026"
            return `${date.getDate()} ${months[date.getMonth()]}, ${date.getFullYear()}`;
        case "Y-m-d": // "2026-08-10"
            return date.toISOString().slice(0, 10);
        case "datetime": // "10 Aug, 2026 08:04"
            return `${date.getDate()} ${months[date.getMonth()]}, ${date.getFullYear()} ${String(date.getHours()).padStart(2, "0")}:${String(date.getMinutes()).padStart(2, "0")}`;
        case "relative": // "2 days ago" — coarse, no external lib
            return formatRelative(date);
        default:
            return date.toLocaleDateString();
    }
}

function formatRelative(date) {
    const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
    const units = [
        ["year", 31536000],
        ["month", 2592000],
        ["day", 86400],
        ["hour", 3600],
        ["minute", 60],
    ];
    for (const [name, secs] of units) {
        const val = Math.floor(seconds / secs);
        if (val >= 1) return `${val} ${name}${val > 1 ? "s" : ""} ago`;
    }

    return "just now";
}
