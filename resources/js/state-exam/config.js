import { baseUri } from "../app";

/**
 * Static configuration for the Student module.
 * No DOM access, no state — safe to import anywhere.
 */
export const CONFIG = {
    API_BASE: baseUri('exam-states'),
    DEBOUNCE_DELAY: 300,
    LOCALE: 'en-GB',
    PER_PAGE: 10, // exam-room counts are small (dozens) — keep the whole list on one page so duplicate room numbers are easy to spot
};
