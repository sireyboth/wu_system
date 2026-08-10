/**
 * Static configuration for the Student module.
 * No DOM access, no state — safe to import anywhere.
 */
export const CONFIG = {
    API_BASE: '/api/v1/exam-states',
    DEBOUNCE_DELAY: 300,
    LOCALE: 'en-GB',
    PER_PAGE: 25, // used once server-side pagination is wired in (see api-service.js note)
};

