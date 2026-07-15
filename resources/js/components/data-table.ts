import apiCRUD from "../lib/config";
import { IConfig, IState } from "../lib/interface";

export default function dataTable(config: IConfig): IState {
    return {
        items: [],
        loading: true,
        search: "",
        sortField: config.defaultSort ?? "created_at",
        sortDir: config.defaultDir ?? "desc",
        page: 1,
        perPage: config.perPage ?? 10,
        totalPages: 1,
        total: 0,
        item: null,
        _searchTimer: null,

        $el: undefined as any,
        $watch: undefined as any,

        init() {
            this.fetchData();
            this.$watch("search", () => {
                this.page = 1;
                if (this._searchTimer) clearTimeout(this._searchTimer);
                this._searchTimer = setTimeout(() => this.fetchData(), 350);
            });
            this.$watch("page", () => this.fetchData());
        },

        async fetchData() {
            this.loading = true;
            try {
                const { data } = await apiCRUD.get(config.endpoint, {
                    params: {
                        search: this.search,
                        sort: this.sortField,
                        dir: this.sortDir,
                        page: this.page,
                        per_page: this.perPage,
                    },
                });

                this.items = data.data;
                this.totalPages = data.meta.last_page;
                this.total = data.meta.total;
            } catch (e) {
                console.error("DataTable fetch failed:", e);
                this.items = [];
            } finally {
                this.loading = false;
            }
        },
        sortBy(field) {
            if (this.sortField === field) {
                this.sortDir = this.sortDir === "asc" ? "desc" : "asc";
            } else {
                this.sortField = field;
                this.sortDir = "asc";
            }
            this.fetchData();
        },

        confirmDelete(row) {
            this.item = row;
            window.dispatchEvent(
                new CustomEvent("open-modal", { detail: "delete-confirm" }),
            );
        },

        async deleteRow() {
            if (!this.item) return;
            try {
                await apiCRUD.delete(`${config.endpoint}/${this.item.id}`);
                this.item = null;
                await this.fetchData();
            } catch (e) {
                console.error("DataTable delete failed:", e);
            }
        },
    };
}
