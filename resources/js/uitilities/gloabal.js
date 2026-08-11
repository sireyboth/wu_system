import { apiCrud } from "./config";

/**
 * Backing controller for <x-core.table-card>. Server is always in charge —
 * this just fetches and displays. No page-number state to track: a fresh
 * param-based fetch has no `page` param (so Laravel defaults to page 1),
 * and every other navigation follows a URL Laravel already generated
 * (meta.links[i].url, links.prev, links.next) — so it's always correct.
 *
 * Expects the endpoint to return Laravel's default Resource-collection
 * pagination envelope:
 *   {
 *     data: [...],
 *     links: { first, last, prev, next },
 *     meta: { current_page, from, to, last_page, total, per_page,
 *              links: [ { url, label, active }, ... ] }
 *   }
 */
export const table = ({
    endpoint,
    sort = null,
    direction = "asc",
    perPage = 10,
    debounceMs = 400,
} = {}) => ({
    endpoint,
    sort,
    direction,
    perPage,

    rows: [],
    meta: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0,
        links: [],
    },
    links: { prev: null, next: null },
    loading: true,
    error: null,
    search: "",

    init() {
        this.fetch();

        this.$watch(
            "search",
            Alpine.debounce(() => this.fetch(), debounceMs),
        );
        this.$watch("perPage", () => this.fetch());
    },

    async fetch(url = null) {
        this.loading = true;
        this.error = null;
        try {
            const { data } = url
                ? await apiCrud.get(url)
                : await apiCrud.get(this.endpoint, {
                      params: {
                          per_page: this.perPage,
                          ...(this.search ? { search: this.search } : {}),
                          ...(this.sort
                              ? {
                                    sort: `${this.direction === "desc" ? "-" : ""}${this.sort}`,
                                }
                              : {}),
                      },
                  });

            this.rows = data.data ?? [];
            this.meta = data.meta ?? this.meta;
            this.links = data.links ?? this.links;
        } catch (e) {
            this.error = "Failed to load data.";
            this.rows = [];
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    // called from <x-core.pagination> — link is one entry of meta.links
    goToPage(link) {
        if (!link?.url) return;
        this.fetch(link.url);
    },

    // called from <x-core.sortable>
    sortBy(field) {
        this.direction =
            this.sort === field && this.direction === "asc" ? "desc" : "asc";
        this.sort = field;
        this.fetch();
    },

    // called from <x-core.actions> after a delete, to refresh the current page in place
    refresh() {
        this.fetch();
    },
});
