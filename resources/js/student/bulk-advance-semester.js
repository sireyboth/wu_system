/**
 * Bulk "advance to a new semester" — the group version of
 * advance-semester.js. Reads the current selection from bulk-select.js
 * (either explicit ids or "all matching current filters") and posts only
 * the fields the registrar actually filled in; everything left at
 * "— No change —" is untouched per-student on the backend.
 */
import { CONFIG } from "./config.js";
import { fillSelectOptions, loadTermOptions } from "./form-utils.js";
import { getById } from "../app.js";
import { registerModalCloser } from "./ui.js";
import { getSelection, clearSelection } from "./bulk-select.js";
import { hasRequiredBulkFilters } from "./filters.js";

const FIELD_IDS = {
    batch_id: "bulk_advance_batch_id",
    major_id: "bulk_advance_major_id",
    shift_id: "bulk_advance_shift_id",
    group_id: "bulk_advance_group_id",
    campus_id: "bulk_advance_campus_id",
    status_id: "bulk_advance_status_id",
};

let lookupsLoaded = false;

function toggle(forceOpen = null) {
    const modal = getById("bulkAdvanceSemesterModal");
    const card = getById("bulkAdvanceSemesterCard");
    if (!modal || !card) return;

    const isOpen = modal.classList.contains("flex");
    const makeOpen = forceOpen !== null ? forceOpen : !isOpen;

    if (makeOpen) {
        modal.classList.remove("invisible");
        modal.classList.add("flex");
        requestAnimationFrame(() => {
            modal.classList.remove("opacity-0");
            card.classList.remove("scale-90", "opacity-0");
            card.classList.add("scale-100", "opacity-100");
        });
    } else {
        modal.classList.add("opacity-0");
        card.classList.remove("scale-100", "opacity-100");
        card.classList.add("scale-90", "opacity-0");
        setTimeout(() => {
            modal.classList.add("invisible");
            modal.classList.remove("flex");
        }, 300);
    }
}

async function loadLookupsOnce(ApiService) {
    if (lookupsLoaded) return;
    const withAll = (url) => `${url}${url.includes("?") ? "&" : "?"}per_page=1000`;

    await Promise.all(
        Object.entries(FIELD_IDS).map(async ([field, elementId]) => {
            const lookupKey = { batch_id: "batches", major_id: "majors", shift_id: "shifts", group_id: "groups", campus_id: "campuses", status_id: "statuses" }[field];
            const { error, data } = await ApiService.request(withAll(CONFIG.API_LOOKUPS[lookupKey]));
            if (error) return;
            fillSelectOptions(getById(elementId), data?.data ?? data ?? []);
        }),
    );
    lookupsLoaded = true;
}

export function initBulkAdvanceSemester(ApiService, onAdvanced) {
    registerModalCloser("bulk-advance-semester", () => toggle(false));

    getById("bulkAdvanceSemesterBtn")?.addEventListener("click", async () => {
        const selection = getSelection();
        if (selection.count === 0) return;

        // Defense in depth — the button is already disabled by
        // bulk-select.js's updateToolbar() until this is true, but the
        // actual mutating request should never fire even if that check
        // somehow gets bypassed (e.g. stale DOM state).
        if (!hasRequiredBulkFilters()) {
            window.Swal?.fire({
                icon: "warning",
                title: "Filter by Batch and Campus first",
                text: "Bulk-advancing requires narrowing the list down to a specific Batch and Campus before it can run.",
            });
            return;
        }

        await loadLookupsOnce(ApiService);
        await loadTermOptions(ApiService, getById("bulkAdvanceSemesterTermId"));

        const scopeLabel = getById("bulkAdvanceSemesterScope");
        if (scopeLabel) {
            scopeLabel.textContent = selection.all
                ? `${selection.count} student(s) — everyone matching the current filters`
                : `${selection.count} student(s) selected`;
        }

        getById("bulkAdvanceSemesterForm")?.reset();
        toggle(true);
    });

    getById("bulkAdvanceSemesterForm")?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');

        const changes = {};
        Object.entries(FIELD_IDS).forEach(([field, elementId]) => {
            const value = getById(elementId)?.value;
            if (value) changes[field] = value;
        });
        const yearLevel = getById("bulk_advance_year_level")?.value;
        if (yearLevel) changes.year_level = yearLevel;
        const semester = getById("bulk_advance_semester")?.value;
        if (semester) changes.semester = semester;

        if (Object.keys(changes).length === 0) {
            const confirmation = await window.Swal?.fire({
                icon: "question",
                title: "Advance to the current semester only?",
                text: "No fields are set to change — this will just move the selected students onto the currently active term, keeping every one of their academic fields exactly as it is.",
                showCancelButton: true,
                confirmButtonColor: "#0d9488",
                confirmButtonText: "Yes, advance them",
            });
            if (!confirmation?.isConfirmed) return;
        }

        const termId = getById("bulkAdvanceSemesterTermId")?.value || null;
        const selection = getSelection();
        const payload = selection.all
            ? { all: true, filters: selection.filters, changes, term_id: termId }
            : { ids: selection.ids, changes, term_id: termId };

        submitBtn.disabled = true;
        const { error, status, data } = await ApiService.request(
            "/api/v1/students-bulk-advance-semester",
            {
                method: "PATCH",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload),
            },
        );

        if (!error) {
            window.Swal?.fire({
                icon: "success",
                title: "Advanced!",
                text: data?.message || "Selected students were advanced to the new semester.",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3500,
            });
            toggle(false);
            clearSelection();
            onAdvanced?.();
        } else {
            const message = status === 422
                ? Object.values(data?.errors ?? {}).flat().join(" ") || data?.message
                : (data?.message || "Could not advance these students.");
            window.Swal?.fire({ icon: "error", title: "Error", text: message });
        }
        submitBtn.disabled = false;
    });
}
