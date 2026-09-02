export const sidebar = () => ({
    isOpen: false,
    isDesktop: false,

    init() {
        let { classList } = document.body;
        const saved = localStorage.getItem("open-sidebar");
        this.isDesktop = window.matchMedia("(min-width: 640px)").matches;
        this.isOpen = saved !== null ? saved === "true" : this.isDesktop;

        // Keep body classes in sync (for any other CSS that might need them)
        this.$watch("isOpen", (value) => {
            localStorage.setItem("open-sidebar", value ? "true" : "false");
            classList.toggle("sidebar-open", value);
            classList.toggle("sidebar-closed", !value);
        });

        // Initial sync
        classList.toggle("sidebar-open", this.isOpen);
        classList.toggle("sidebar-closed", !this.isOpen);

        // React to viewport changes
        const mq = window.matchMedia("(min-width: 640px)");
        mq.addEventListener("change", (e) => {
            this.isDesktop = e.matches;
            const saved = localStorage.getItem("open");
            this.isOpen = saved !== null ? saved === "true" : this.isDesktop;
        });
    },

    toggle() {
        this.isOpen = !this.isOpen;
    },

    closeOnMobile() {
        if (!this.isDesktop) this.isOpen = false;
    },

    toggleTheme() {
        const isDark = document.documentElement.classList.toggle("dark");
        localStorage.theme = isDark ? "dark" : "light";
    }
});
