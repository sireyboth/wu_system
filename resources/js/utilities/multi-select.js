import { apiCrud } from "./config";

export const multiSelect = (config = {}) => ({
    open: false,
    search: "",
    options: config.options || [],
    valueKey: config.valueKey || "id",
    labelKey: config.labelKey || "name",
    modelName: config.modelName,
    placeholder: config.placeholder || "Select...",

    get filtered() {
        if (!this.search) return this.options;
        return this.options.filter((item) =>
            String(item[this.labelKey] || item)
                .toLowerCase()
                .includes(this.search.toLowerCase()),
        );
    },

    get selectedCount() {
        return (this.form[this.modelName] || []).length;
    },

    get displayText() {
        if (this.selectedCount === 0) return this.placeholder;
        if (this.selectedCount === 1) {
            const id = this.form[this.modelName][0];
            const item = this.options.find(
                (o) => String(o[this.valueKey] || o) === String(id),
            );
            return item ? item[this.labelKey] || item : this._lebel();
        }
        return this._lebel();
    },

    _lebel() {
        return `Items ${this.selectedCount} selected`;
    },

    clear() {
        this.form[this.modelName] = [];
    },

    // Optional: load from API
    async load(url) {
        try {
            const { data } = await apiCrud.get(url);
            this.options = data.data || data;
        } catch (e) {
            console.error(e);
        }
    },
});
