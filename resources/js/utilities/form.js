import { apiCrud } from "./config";

/**
 * Pairs with a Breeze <x-modal name="..."> to handle Add/Edit/View for one
 * resource. Listens for the same "open-modal" event the modal itself
 * listens for, but reads the extra "mode"/"id" the modal ignores.
 *
 * Register once in resources/js/app.js:
 *
 *   import resourceForm from './factories/resourceForm';
 *   Alpine.data('resourceForm', resourceForm);
 *
 * Use in Blade, inside the modal's slot:
 *
 *   <x-modal name="product-form" max-width="md">
 *       <div x-data="resourceForm({ name: 'product-form', endpoint: '/products' })">
 *           ...
 *       </div>
 *   </x-modal>
 *
 * Open it from anywhere (doesn't need to share an x-data scope, $dispatch
 * bubbles a window event):
 *
 *   Add:  $dispatch('open-modal', { name: 'product-form', mode: 'create' })
 *   Edit: $dispatch('open-modal', { name: 'product-form', mode: 'edit', id: row.id })
 *   View: $dispatch('open-modal', { name: 'product-form', mode: 'view', id: row.id })
 */
export const resourceForm = (config = {}) => ({
    name: config.name,
    endpoint: config.endpoint,
    defaults: config.defaults ?? {},

    mode: "create", // create | edit | view
    id: null,
    form: {},
    errors: {},
    loading: false,

    init() {
        window.addEventListener("open-modal", (event) => {
            const detail =
                typeof event.detail === "string"
                    ? { name: event.detail }
                    : event.detail;

            if (detail.name !== this.name) return;

            this.errors = {};
            this.mode = detail.mode ?? "create";
            this.id = detail.id ?? null;

            if (this.mode === "create") {
                this.form = { ...this.defaults };
            } else {
                this.fetchRecord();
            }
        });
    },

    async fetchRecord() {
        this.loading = true;
        try {
            const { data } = await apiCrud.get(`${this.endpoint}/${this.id}`);
            this.form = data.data ?? data;
        } catch (e) {
            console.error("resourceForm: fetch failed", e);
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

            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: this.name }),
            );
            window.dispatchEvent(new CustomEvent("table-refresh"));
        } catch (e) {
            e.response?.status === 422
                ? (this.errors = e.response.data.errors)
                : console.error("resourceForm: submit failed", e);
        } finally {
            this.loading = false;
        }
    },
});
