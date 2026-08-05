/**
 * core.js — small, always-touched-together infrastructure pieces.
 * These have no logic of their own; they exist to be imported by
 * files that DO have logic (api-service, student-form, index, etc.)
 */

import { getById } from "../app";

/**
 * Single shared mutable state object for the Student module.
 * Deliberately NOT frozen/exported as individual consts — every consumer
 * needs to read/write the *same* object reference.
 */
export const state = {
    isEditMode: false,
    editingStudentId: null,
    debounceTimer: null,
    searchAbortController: null, // used by api-service to cancel stale searches
};

/**
 * Builds a fresh DOM selector map. Call once, on DOMContentLoaded,
 * from index.js and pass the result into everything else.
 *
 * Kept as a factory (not a module-level constant) so other page modules
 * (lecturers, guardians, etc.) can each build their own shape without
 * fighting over a shared `window.DOM`.
 */
export function buildDom() {
    return {
        form: getById("studentForm"),
        tableBody: getById("student-table-body"),
        searchInput: getById("studentSearchInput"),
        loader: getById("loading-overlay"),
        modal: getById("studentModal"),
        modalCard: getById("modalCard"),
        modalTitle: getById("modalTitle"),
        submitBtn: getById("studentForm")?.querySelector(
            'button[type="submit"]',
        ),
    };
}

/**
 * Thin wrapper around SweetAlert2's toast mixin.
 * Falls back to console.log so the app doesn't crash on pages
 * that forget to load SweetAlert2.
 */
export const Toast =
    typeof Swal !== "undefined"
        ? Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
          })
        : { fire: (opts) => console.log("[Toast fallback]", opts) };
