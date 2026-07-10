/**
 * Static configuration for the Student module.
 * No DOM access, no state — safe to import anywhere.
 */
export const CONFIG = {
    API_BASE: '/api/v1/students',
    DEBOUNCE_DELAY: 300,
    LOCALE: 'en-GB',
    PER_PAGE: 25, // used once server-side pagination is wired in (see api-service.js note)
};

CONFIG.API_LOOKUPS = {
    provinces: '/api/v1/provinces',
    districts: '/api/v1/districts',
    communes: '/api/v1/communes',
    villages: '/api/v1/villages',
    batches: '/api/v1/batches',
    majors: '/api/v1/majors',
};
