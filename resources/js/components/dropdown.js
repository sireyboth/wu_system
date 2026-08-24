export const dropdown = (config = {}) => ({
    open: false,
    style: {},
    placement: config.placement || "bottom-end",
    offset: config.offset || 8,

    placements: [
        "bottom-end",
        "bottom-start",
        "bottom",
        "top-end",
        "top-start",
        "top",
        // "right-start",
        // "right-end",
        // "right",
        // "left-start",
        // "left-end",
        // "left",
    ],

    init() {
        // Close on any scroll (table, modal, page…)
        this._onScroll = () => this.close();
        window.addEventListener("scroll", this._onScroll, true);

        // Close on resize
        this._onResize = () => this.close();
        window.addEventListener("resize", this._onResize);

        // Click outside (works with teleport)
        this._onClickOutside = (e) => {
            const trigger = this.$refs.trigger;
            const menu = this.$refs.menu;

            if (
                this.open &&
                trigger &&
                menu &&
                !trigger.contains(e.target) &&
                !menu.contains(e.target)
            )
                this.close();
        };
        document.addEventListener("click", this._onClickOutside, true);

        // ★ Close when modal opens
        this._onOpenModal = () => this.close();
        window.addEventListener("open-modal", this._onOpenModal);

        window.addEventListener("close-dropdown", () => this.close());
    },

    destroy() {
        window.removeEventListener("scroll", this._onScroll, true);
        window.removeEventListener("resize", this._onResize);
        document.removeEventListener("click", this._onClickOutside, true);
        window.removeEventListener("open-modal", this._onOpenModal); // ★
    },

    toggle() {
        this.open ? this.close() : this.openMenu();
    },

    openMenu() {
        this.open = true;
        this.$nextTick(() => this.updatePosition());
    },

    close() {
        this.open = false;
    },

    updatePosition() {
        const trigger = this.$refs.trigger;
        const menu = this.$refs.menu;
        if (!trigger || !menu) return;

        const tRect = trigger.getBoundingClientRect();
        const mRect = menu.getBoundingClientRect();
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const gap = this.offset;
        const order = [
            this.placement,
            ...this.placements.filter((p) => p !== this.placement),
        ];

        let best = null;

        for (const place of order) {
            const pos = this.calc(place, tRect, mRect, gap);

            if (
                pos.left >= 0 &&
                pos.top >= 0 &&
                pos.left + mRect.width <= vw &&
                pos.top + mRect.height <= vh
            ) {
                best = pos;
                break;
            }
            if (!best) best = pos;
        }

        // Clamp so it never goes outside the viewport
        best.left = Math.max(
            this.offset,
            Math.min(best.left, vw - mRect.width - this.offset),
        );
        best.top = Math.max(
            this.offset,
            Math.min(best.top, vh - mRect.height - this.offset),
        );

        this.style = {
            position: "fixed",
            left: `${best.left}px`,
            top: `${best.top}px`,
            zIndex: 9999,
        };
    },

    calc(place, t, m, gap) {
        switch (place) {
            case "bottom":
                return {
                    left: t.left + (t.width - m.width) / 2,
                    top: t.bottom + gap,
                };
            case "bottom-start":
                return { left: t.left, top: t.bottom + gap };
            case "bottom-end":
                return { left: t.right - m.width, top: t.bottom + gap };

            case "top":
                return {
                    left: t.left + (t.width - m.width) / 2,
                    top: t.top - m.height - gap,
                };
            case "top-start":
                return { left: t.left, top: t.top - m.height - gap };
            case "top-end":
                return { left: t.right - m.width, top: t.top - m.height - gap };

            case "right":
                return {
                    left: t.right + gap,
                    top: t.top + (t.height - m.height) / 2,
                };
            case "right-start":
                return { left: t.right + gap, top: t.top };
            case "right-end":
                return { left: t.right + gap, top: t.bottom - m.height };

            case "left":
                return {
                    left: t.left - m.width - gap,
                    top: t.top + (t.height - m.height) / 2,
                };
            case "left-start":
                return { left: t.left - m.width - gap, top: t.top };
            case "left-end":
                return {
                    left: t.left - m.width - gap,
                    top: t.bottom - m.height,
                };

            default:
                return { left: t.left, top: t.bottom + gap };
        }
    },
});
