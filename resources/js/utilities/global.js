import apiCRUD from "./config";

export default function listTable(config = {}) {
    let searchTimer = null;

    return {
        // --- config ---------------------------------------------------
        endpoint: config.endpoint,
        perPage: config.perPage ?? 10,

        // --- state ------------------------------------------------------
        rows: [],
        meta: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
        filters: config.filters ?? {},
        search: "",
        sortField: config.sort ?? null,
        sortDirection: config.direction ?? "asc",
        loading: false,

        // --- lifecycle --------------------------------------------------
        init() {
            this.fetchData();
        },

        // --- search -------------------------------------------------------
        onSearchInput() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => this.fetchData(1), 350);
        },

        // --- sorting -------------------------------------------------------
        sort(field) {
            if (this.sortField === field) {
                this.sortDirection =
                    this.sortDirection === "asc" ? "desc" : "asc";
            } else {
                this.sortField = field;
                this.sortDirection = "asc";
            }
            this.fetchData(1);
        },

        // --- filters -------------------------------------------------------
        toggleFilter(key, value) {
            const list = this.filters[key] ?? (this.filters[key] = []);
            const i = list.indexOf(value);
            i === -1 ? list.push(value) : list.splice(i, 1);
            this.fetchData(1);
        },

        // --- pagination -------------------------------------------------------
        goToPage(page) {
            if (
                page < 1 ||
                page > this.meta.last_page ||
                page === this.meta.current_page
            )
                return;
            this.fetchData(page);
        },

        // Windowed page list for the pager, e.g. [1, '...', 4, 5, 6, '...', 42]
        pages() {
            const total = this.meta.last_page;
            const current = this.meta.current_page;
            if (total <= 7)
                return Array.from({ length: total }, (_, i) => i + 1);

            const pages = [1];
            if (current > 3) pages.push("...");
            for (
                let p = Math.max(2, current - 1);
                p <= Math.min(total - 1, current + 1);
                p++
            ) {
                pages.push(p);
            }
            if (current < total - 2) pages.push("...");
            pages.push(total);
            return pages;
        },

        // --- data fetching -------------------------------------------------------
        async fetchData(page = this.meta.current_page) {
            this.loading = true;

            try {
                const { data } = await apiCRUD.get(this.endpoint, {
                    params: {
                        page,
                        per_page: this.perPage,
                        search: this.search,
                        sort_field: this.sortField ?? "",
                        sort_direction: this.sortDirection,
                        ...this.flattenFilters(),
                    },
                });
                this.rows = data.data;
                this.meta = data.meta;
                console.table(this.rows);

            } catch (e) {
                console.error("listTable: fetch failed", e);
            } finally {
                this.loading = false;
            }
        },

        flattenFilters() {
            const out = {};
            for (const [key, value] of Object.entries(this.filters)) {
                if (Array.isArray(value) && value.length)
                    out[key] = value.join(",");
            }
            return out;
        },

        // --- row actions -------------------------------------------------------
        async destroy(row, { confirmMessage = "Delete this record?" } = {}) {
            if (!confirm(confirmMessage)) return;

            try {
                await apiCRUD.delete(`${this.endpoint}/${row.id}`);
                this.fetchData();
            } catch (e) {
                console.error("listTable: delete failed", e);
            }
        },
    };
}
