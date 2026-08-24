import Alpine from "alpinejs";
import { apiCrud } from "../utils/config";
import { showConfirm, showError, showMessage } from "../utils/helper";

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
    searchField = null,
} = {}) => ({
    endpoint,
    sort,
    direction,
    perPage,
    searchField,

    items: [],
    meta: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0,
        links: [],
    },
    links: { prev: null, next: null },
    loading: false,
    error: null,
    search: "",

    init() {
        this.fetch();

        // Keep current page when refreshing after create/update/delete
        window.addEventListener("table-refresh", () => this.refresh());

        this.$watch(
            "search",
            Alpine.debounce(() => {
                this.meta.current_page = 1; // reset to page 1 only when searching
                this.fetch();
            }, debounceMs),
        );

        this.$watch("perPage", () => {
            this.meta.current_page = 1;
            this.fetch();
        });

        this.$watch("searchField", () => {
            if (this.search) {
                this.meta.current_page = 1;
                this.fetch();
            }
        });
    },

    async fetch(url = null, force = false) {
        if (this.loading && !force) return;

        this.loading = true;
        this.error = null;

        try {
            let response;

            if (url) {
                // Coming from pagination link
                response = await apiCrud.get(url);
            } else {
                // Normal fetch (with current state)
                response = await apiCrud.get(this.endpoint, {
                    params: {
                        page: this.meta.current_page || 1,
                        per_page: this.perPage,
                        ...(this.search ? { search: this.search } : {}),
                        ...(this.search && this.searchField
                            ? { by: this.searchField }
                            : {}),
                        ...(this.sort
                            ? {
                                sort: `${this.direction === "desc" ? "-" : ""}${this.sort}`,
                            }
                            : {}),
                    },
                });
            }

            const data = response.data;

            this.items = data.data ?? data ?? [];
            this.meta = data.meta ?? {
                current_page: 1,
                last_page: 1,
                total: 0,
                from: 0,
                to: 0,
                links: [],
            };
            this.links = data.links ?? { prev: null, next: null };
        } catch (e) {
            this.error = "Failed to load data.";
            this.items = [];
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    // Called from <x-core.pagination>
    goToPage(link) {
        if (!link?.url || link.active) return;
        this.fetch(link.url);
    },

    // Called from <x-core.sortable>
    sortBy(field) {
        if (this.sort === field) {
            this.direction = this.direction === "asc" ? "desc" : "asc";
        } else {
            this.sort = field;
            this.direction = "asc";
        }
        this.meta.current_page = 1; // usually better to reset page when sorting
        this.fetch();
    },

    // Force refresh current page (used after delete, etc)
    refresh() {
        this.fetch(null, true);
    },

    // Optional: reset everything and go to page 1
    resetAndFetch() {
        this.search = "";
        this.sort = null;
        this.direction = "asc";
        this.meta.current_page = 1;
        this.refresh();
    },
});
