export const modal = (name, open) => ({
    name,
    open,

    focusables() {
        let selector =
            "a, button, input:not([type='hidden']), textarea, select, details, [tabindex]:not([tabindex='-1'])";
        return [...this.$el.querySelectorAll(selector)].filter(
            (el) => !el.hasAttribute("disabled"),
        );
    },

    firstFocusable() {
        return this.focusables()[0];
    },

    lastFocusable() {
        return this.focusables().slice(-1)[0];
    },

    nextFocusable() {
        return (
            this.focusables()[this.nextFocusableIndex()] ||
            this.firstFocusable()
        );
    },

    prevFocusable() {
        return (
            this.focusables()[this.prevFocusableIndex()] || this.lastFocusable()
        );
    },

    nextFocusableIndex() {
        return (
            (this.focusables().indexOf(document.activeElement) + 1) %
            (this.focusables().length + 1)
        );
    },

    prevFocusableIndex() {
        return (
            Math.max(0, this.focusables().indexOf(document.activeElement)) - 1
        );
    },

    init() {
        // Watch open for body scroll lock
        this.$watch("open", (value) => {
            const { classList } = document.body;
            value
                ? classList.add("overflow-y-hidden")
                : classList.remove("overflow-y-hidden");
        });

        // Listen for open-modal
        this.$watch("$el", () => {}); // just to make sure

        window.addEventListener("open-modal", (e) => {
            const detail = e.detail;
            const eventName = typeof detail === "object" ? detail.name : detail;

            if (eventName === this.name) {
                this.open = true;
            }
        });

        // Listen for close-modal
        window.addEventListener("close-modal", (e) => {
            const detail = e.detail;
            const eventName = typeof detail === "object" ? detail.name : detail;

            if (eventName === this.name) {
                this.open = false;
            }
        });
    },

    close() {
        this.open = false;
    },
});
