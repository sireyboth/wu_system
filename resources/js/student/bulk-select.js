/**
 * Row selection for the student list — checkboxes + the "N selected" bulk
 * action bar. Selection can be either an explicit set of ids (the normal
 * case: check a few rows by hand) or "every student matching the current
 * filters" (when there are more matches than fit on one page and the
 * registrar wants literally all of them, not just what's visible).
 *
 * Deliberately knows nothing about what a "bulk action" does — it just
 * tracks selection and exposes it. bulk-advance-semester.js reads that
 * state when the action button is clicked.
 */
import { getById } from "../app.js";
import { getActiveFilters } from "./filters.js";

const selectedIds = new Set();
let selectAllMatchingFilters = false;
let lastMeta = null;

export function getSelection() {
    if (selectAllMatchingFilters) {
        return { all: true, filters: getActiveFilters(), count: lastMeta?.total ?? 0 };
    }
    return { all: false, ids: Array.from(selectedIds), count: selectedIds.size };
}

export function clearSelection() {
    selectedIds.clear();
    selectAllMatchingFilters = false;
    updateToolbar();
}

function updateToolbar() {
    const bar = getById("bulkActionBar");
    const countLabel = getById("bulkSelectedCount");
    const selectAllLink = getById("bulkSelectAllFilteredBtn");
    const { all, count } = getSelection();

    if (bar) bar.classList.toggle("hidden", count === 0);
    if (bar) bar.classList.toggle("flex", count > 0);
    if (countLabel) {
        countLabel.textContent = all
            ? `${count} selected (all matching current filters)`
            : `${count} selected`;
    }

    if (selectAllLink) {
        const currentPageCount = document.querySelectorAll(".row-checkbox").length;
        const moreExist = !all && lastMeta && lastMeta.total > currentPageCount && selectedIds.size === currentPageCount && selectedIds.size > 0;
        selectAllLink.classList.toggle("hidden", !moreExist);
        selectAllLink.textContent = lastMeta ? `Select all ${lastMeta.total} matching current filters` : "";
    }
}

function syncCheckboxesToState() {
    const rowBoxes = document.querySelectorAll(".row-checkbox");
    rowBoxes.forEach((box) => {
        box.checked = selectAllMatchingFilters || selectedIds.has(box.getAttribute("data-id"));
    });

    const selectAll = getById("selectAllCheckbox");
    if (selectAll) {
        const total = rowBoxes.length;
        const checkedCount = Array.from(rowBoxes).filter((b) => b.checked).length;
        selectAll.checked = total > 0 && checkedCount === total;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < total;
    }

    updateToolbar();
}

export function initBulkSelect() {
    document.addEventListener("students:rendered", syncCheckboxesToState);
    document.addEventListener("students:meta", (e) => {
        lastMeta = e.detail;
        updateToolbar();
    });

    document.addEventListener("change", (e) => {
        const box = e.target.closest(".row-checkbox");
        if (!box) return;

        selectAllMatchingFilters = false;
        const id = box.getAttribute("data-id");
        if (box.checked) selectedIds.add(id);
        else selectedIds.delete(id);
        syncCheckboxesToState();
    });

    getById("selectAllCheckbox")?.addEventListener("change", (e) => {
        selectAllMatchingFilters = false;
        const rowBoxes = document.querySelectorAll(".row-checkbox");
        if (e.target.checked) {
            rowBoxes.forEach((box) => selectedIds.add(box.getAttribute("data-id")));
        } else {
            rowBoxes.forEach((box) => selectedIds.delete(box.getAttribute("data-id")));
        }
        syncCheckboxesToState();
    });

    getById("bulkSelectAllFilteredBtn")?.addEventListener("click", () => {
        selectAllMatchingFilters = true;
        syncCheckboxesToState();
    });
}
