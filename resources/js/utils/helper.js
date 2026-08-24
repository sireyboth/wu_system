import Swal from "sweetalert2";

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

// Success Toast
export const showMessage = (
    message = "Success",
    position = "top-end",
    timer = 1600,
) =>
    Swal.fire({
        toast: true,
        position,
        icon: "success",
        title: message,
        showConfirmButton: false,
        timer,
        timerProgressBar: true,
    });

// Error Alert
export const showError = (
    message = "Something went wrong!",
    title = "Oops...",
) =>
    Swal.fire({
        icon: "error",
        title,
        text: message,
    });

// Confirmation Dialog (for Delete / Edit)
export const showConfirm = ({
    title = "Are you sure?",
    text = "You won't be able to revert this!",
    confirmText = "Yes, do it!",
    icon = "warning",
    confirmColor = "#ef4444",
} = {}) =>
    Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: "#6b7280",
        confirmButtonText: confirmText,
        cancelButtonText: "Cancel",
        reverseButtons: true,
    });

export function showValidations(errors, title = "Validation Error") {
    // errors = { name: ["The name field is required."], email: ["The email must be valid."] }

    let html = '<ul class="text-left list-disc list-inside space-y-1">';

    Object.values(errors).forEach((messages) => {
        messages.forEach((msg) => {
            html += `<li>${msg}</li>`;
        });
    });

    html += "</ul>";

    return Swal.fire({
        icon: "error",
        title,
        html: html,
        confirmButtonText: "OK",
        confirmButtonColor: "#ef4444",
    });
}

export function showFirstValidation(errors, title = "Validation Error") {
    const firstError = Object.values(errors)[0][0];

    return Swal.fire({
        icon: "error",
        title,
        text: firstError,
    });
}

export const getStatus = (value) => (value ? "Active" : "Inactive");
