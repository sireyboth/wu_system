/**
 * Reads the student list's filter bar (Major/Batch/Shift/Group/Campus/
 * Status selects) — shared between the list's own query string and the
 * "advance whole filtered group" bulk action, so both always agree on
 * what's currently filtered.
 */
import { getById } from "../app.js";

const FILTER_FIELDS = [
    "major_id",
    "batch_id",
    "shift_id",
    "group_id",
    "campus_id",
    "status_id",
];

const filterElementId = (field) => `filter_${field}`;

export function getActiveFilters() {
    const filters = {};
    FILTER_FIELDS.forEach((field) => {
        const value = getById(filterElementId(field))?.value;
        if (value) filters[field] = value;
    });
    return filters;
}

export function activeFiltersQueryString() {
    const params = new URLSearchParams(getActiveFilters());
    return params.toString();
}

export function hasActiveFilters() {
    return Object.keys(getActiveFilters()).length > 0;
}

export function initFilterBar(onFilterChange) {
    FILTER_FIELDS.forEach((field) => {
        getById(filterElementId(field))?.addEventListener("change", onFilterChange);
    });
    getById("filterClearBtn")?.addEventListener("click", () => {
        FILTER_FIELDS.forEach((field) => {
            const el = getById(filterElementId(field));
            if (el) el.value = "";
        });
        onFilterChange();
    });
}

export async function populateFilterBar(ApiService) {
    const { fillSelectOptions } = await import("./form-utils.js");
    const { CONFIG } = await import("./config.js");

    const lookups = {
        major_id: CONFIG.API_LOOKUPS.majors,
        batch_id: CONFIG.API_LOOKUPS.batches,
        shift_id: CONFIG.API_LOOKUPS.shifts,
        group_id: CONFIG.API_LOOKUPS.groups,
        campus_id: CONFIG.API_LOOKUPS.campuses,
        status_id: CONFIG.API_LOOKUPS.statuses,
    };

    await Promise.all(
        Object.entries(lookups).map(async ([field, url]) => {
            const { error, data } = await ApiService.request(`${url}?per_page=1000`);
            if (error) return;
            fillSelectOptions(getById(filterElementId(field)), data?.data ?? data ?? []);
        }),
    );
}
