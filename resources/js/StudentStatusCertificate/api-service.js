/**
 * Creates an API service bound to a given DOM reference map
 * (needed only for the loading-overlay toggle).
 *
 * Usage:
 *   const ApiService = createApiService(dom);
 *   const { error, status, data } = await ApiService.request(url, { method: 'POST', ... });
 *
 * Supports an AbortController via options.signal, same as native fetch —
 * pass one in to cancel a stale in-flight request (see student-form.js
 * loadStudents(), which cancels superseded search requests).
 */
export function createApiService(dom) {
    return {
        async request(url, options = {}) {
            this.toggleLoader(true);
            try {
                const { headers, method = 'GET', body, signal, ...restOptions } = options;
                const response = await fetch(url, {
                    method,
                    credentials: 'omit',
                    headers: {
                        Accept: 'application/json',
                        ...headers,
                    },
                    body,
                    signal,
                    ...restOptions,
                });

                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                const result = isJson ? await response.json() : null;

                return { error: !response.ok, status: response.status, data: result };
            } catch (err) {
                if (err.name === 'AbortError') {
                    // Superseded by a newer request — not a real error, caller should ignore.
                    return { error: true, aborted: true, status: 0, data: null };
                }
                console.error(`[API Error] Action failed on ${url}:`, err);
                return { error: true, status: 500, data: null };
            } finally {
                this.toggleLoader(false);
            }
        },
        toggleLoader(show) {
            if (dom.loader) dom.loader.classList.toggle('hidden', !show);
        },
    };
}
