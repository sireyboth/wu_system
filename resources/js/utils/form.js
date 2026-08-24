import { apiCrud } from "./config";
import { showConfirm, showError, showMessage, showValidations } from "./helper";

export const iform = (config = {}) => ({
    // ===== Shared State =====
    name: config.name,
    endpoint: config.endpoint,
    mode: "create",
    id: null,
    form: {},
    errors: {},
    loading: false,
    open: false,

    // ===== Shared Methods =====
    init() {
        window.addEventListener("open-modal", (e) => this.handleOpen(e.detail));
        window.addEventListener("close-modal", (e) => {
            if (e.detail === this.name) this.close();
        });
        window.addEventListener("table-action", (e) => {
            this.id = e.detail?.id ?? null;
            this.handleAction(e.detail);
        });
    },

    async handleOpen(detail) {
        if (detail.name !== this.name) return;

        this.mode = detail?.mode ?? "create";
        this.id = detail?.id ?? null;
        this.errors = {};
        this.open = true;
        this.loading = true;

        try {
            if (this.mode === "create") this.form = this.getEmptyForm();
            else await this.fetch(detail?.item ?? null);

            if (typeof this.onOpen === "function") await this.onOpen(detail);
        } finally {
            this.loading = false;
        }
    },

    async fetch(item = null) {
        this.loading = true;
        try {
            // Case 1: Object passed → show instantly
            if (item) this.form = JSON.parse(JSON.stringify(item));

            // Case 2: Always try to get fresh data if we have id
            if (this.id) {
                const { data } = await apiCrud.get(
                    `${this.endpoint}/${this.id}`,
                );
                this.form = data.data ?? data;
            }
        } catch (e) {
            await showError(e.response?.data?.message || "Failed to load");
            this.close();
        } finally {
            this.loading = false;
        }
    },

    async submit() {
        if (this.mode === "view") return;

        this.loading = true;
        this.errors = {};

        try {
            this.mode === "create"
                ? await apiCrud.post(this.endpoint, this.form)
                : await apiCrud.put(`${this.endpoint}/${this.id}`, this.form);

            this.close();
            window.dispatchEvent(new CustomEvent("table-refresh"));
            await showMessage(
                this.mode === "create"
                    ? "Created successfully"
                    : "Updated successfully",
            );
        } catch (e) {
            if (e.response?.status === 422) {
                // 1. Keep field errors for form display
                this.errors = e.response.data.errors;
                console.error(this.errors);

                // 2. Also show in SweetAlert2
                await showValidations(this.errors);
            } else {
                showError(e.response?.data?.message || "Something went wrong!");
            }
        } finally {
            this.loading = false;
        }
    },

    close() {
        this.open = false;
        this.form = {};
        this.errors = {};
    },

    // ===== Must be overridden by child =====
    getEmptyForm() {
        return {};
    },

    async handleAction(detail = {}) {
        // Close any open dropdown first
        window.dispatchEvent(new CustomEvent("close-dropdown"));

        const { title, text, confirmText, success, action } = detail;
        const configs = {
            // Soft Delete
            trash: {
                title: title ?? "Move to Trash?",
                text: text ?? "This item will be moved to trash.",
                confirmText: confirmText ?? "Yes, trash it!",
                success: success ?? "Moved to trash",
                method: "delete",
                url: `${this.endpoint}/${this.id}`,
                confirmColor: "#f59e0b", // orange
            },

            // Restore
            restore: {
                title: title ?? "Restore Item?",
                text: text ?? "This item will be restored.",
                confirmText: confirmText ?? "Yes, restore it!",
                success: success ?? "Restored successfully",
                method: "patch",
                url: `${this.endpoint}/${this.id}/restore`,
                confirmColor: "#10b981", // green
                icon: "question",
            },

            // Force Delete (Permanent)
            empty: {
                title: title ?? "Delete Permanently?",
                text: text ?? "This action cannot be undone!",
                confirmText: confirmText ?? "Yes, delete permanently!",
                success: success ?? "Deleted permanently",
                method: "delete",
                url: `${this.endpoint}/${this.id}/empty`,
                confirmColor: "#ef4444", // red
            },
        };

        const config = configs[action];

        if (!config) {
            console.error("Unknown action:", action);
            return;
        }

        const result = await showConfirm({
            title: config.title,
            text: config.text,
            confirmText: config.confirmText,
            icon: config.icon ?? "warning",
            confirmColor: config.confirmColor,
        });
        if (!result.isConfirmed) return;

        this.loading = true;
        this.errors = {};
        try {
            if (config.method === "delete") {
                await apiCrud.delete(config.url);
            } else {
                await apiCrud.patch(config.url);
            }

            window.dispatchEvent(new CustomEvent("table-refresh"));
            await showMessage(config.success);
        } catch (error) {
            this.errors = e.response.data.errors;
            showError(error.response?.data?.message || "Action failed");
        }
    },
});
