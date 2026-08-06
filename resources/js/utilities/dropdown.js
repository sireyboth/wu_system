export const dropdown = () => ({
    open: false,
    style: "",
    placement: "bottom-end",
    offset: 8,
    matchWidth: false,

    init() {
        this.placement = this.$el.dataset.placement || "bottom-end";
        this.offset = parseInt(this.$el.dataset.offset || 8);
        this.matchWidth = this.$el.dataset.matchWidth === "true";
    },

    toggle() {
        if (this.open) {
            this.open = false;
            return;
        }

        const rect = this.$refs.trigger.getBoundingClientRect();
        const offset = this.offset;
        const placement = this.placement;

        let top = 0;
        let left = 0;
        let right = "auto";
        let bottom = "auto";
        let transform = "";
        let width = "";

        // Vertical placements
        if (placement.startsWith("bottom")) {
            top = rect.bottom + offset;
        } else if (placement.startsWith("top")) {
            top = rect.top - offset;
            transform = "translateY(-100%)";
        }

        // Horizontal placements
        if (placement.startsWith("left")) {
            left = rect.left - offset;
            transform = "translateX(-100%)";
            top = rect.top;
        } else if (placement.startsWith("right")) {
            left = rect.right + offset;
            top = rect.top;
        }

        // Alignment
        if (
            placement.endsWith("start") ||
            placement === "left-start" ||
            placement === "right-start"
        ) {
            if (placement.startsWith("bottom") || placement.startsWith("top")) {
                left = rect.left;
            }
        }

        if (
            placement.endsWith("end") ||
            placement === "left-end" ||
            placement === "right-end"
        ) {
            if (placement.startsWith("bottom") || placement.startsWith("top")) {
                left = "auto";
                right = window.innerWidth - rect.right;
            } else if (
                placement.startsWith("left") ||
                placement.startsWith("right")
            ) {
                top = rect.bottom;
                transform += " translateY(-100%)";
            }
        }

        if (placement === "left-start" || placement === "right-start") {
            top = rect.top;
            transform = placement.startsWith("left") ? "translateX(-100%)" : "";
        }
        if (placement === "left-end" || placement === "right-end") {
            top = rect.bottom;
            transform =
                (placement.startsWith("left") ? "translateX(-100%) " : "") +
                "translateY(-100%)";
        }

        // Match trigger width
        if (this.matchWidth) {
            width = `width: ${rect.width}px;`;
        }

        this.style = `
            top: ${top}px;
            left: ${typeof left === "number" ? left + "px" : left};
            right: ${typeof right === "number" ? right + "px" : right};
            bottom: ${bottom};
            transform: ${transform};
            ${width}
        `;

        this.open = true;
    },

    close() {
        this.open = false;
    },
});
