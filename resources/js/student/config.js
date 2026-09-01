import { baseUri } from "../app";

/**
 * Static configuration for the Student module.
 * No DOM access, no state — safe to import anywhere.
 */
export const CONFIG = {
    API_BASE: baseUri("students"),
    DEBOUNCE_DELAY: 300,
    LOCALE: "en-GB",
    PER_PAGE: 10,
};

CONFIG.API_LOOKUPS = {
    provinces: baseUri("provinces"),
    districts: baseUri("districts"),
    communes: baseUri("communes"),
    villages: baseUri("villages"),
    batches: baseUri("batches"),
    majors: baseUri("majors"),
    statuses: baseUri("statuses"),
    groups: baseUri("groups"),
    shifts: baseUri("shifts"),
    nationalities: baseUri("nationalities"),
};
