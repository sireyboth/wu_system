export const datepicker = (fieldName, format = "Y-m-d") => ({
    open: false,
    view: "days", // days | months | years
    placement: { top: 0, left: 0 },
    month: new Date().getMonth(),
    year: new Date().getFullYear(),
    format,

    monthNames: [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ],
    shortMonthNames: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ],

    // ===== Helpers =====
    formatDate(date) {
        const d = String(date.getDate()).padStart(2, "0");
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const Y = date.getFullYear();

        return this.format
            .replace("Y", Y)
            .replace("m", m)
            .replace("d", d)
            .replace("M", this.shortMonthNames[date.getMonth()]);
    },

    parseDate(str) {
        if (!str) return null;

        let date = new Date(str);
        if (!isNaN(date)) return date;

        const parts = str.split(/[-/.]/);
        if (parts.length !== 3) return null;

        let day, month, year;

        if (this.format.startsWith("Y")) {
            [year, month, day] = parts;
        } else if (this.format.startsWith("d")) {
            [day, month, year] = parts;
        } else if (this.format.startsWith("m")) {
            [month, day, year] = parts;
        } else {
            return null;
        }

        date = new Date(year, month - 1, day);
        return isNaN(date) ? null : date;
    },

    // ===== Computed =====
    get daysInMonth() {
        return new Date(this.year, this.month + 1, 0).getDate();
    },

    get blanks() {
        const day = new Date(this.year, this.month, 1).getDay();
        return day === 0 ? 6 : day - 1; // Monday first
    },

    get yearRangeStart() {
        return Math.floor(this.year / 10) * 10;
    },

    get yearsInRange() {
        const start = this.yearRangeStart;
        return Array.from({ length: 12 }, (_, i) => start - 1 + i);
    },

    get yearRangeLabel() {
        const start = this.yearRangeStart;
        return `${start} - ${start + 9}`;
    },

    // ===== Actions =====
    toggle() {
        if (this.open) {
            this.open = false;
            return;
        }

        // Sync calendar to current value
        const current = this.parseDate(this.form[fieldName]);
        if (current) {
            this.month = current.getMonth();
            this.year = current.getFullYear();
        }

        const rect = this.$refs.trigger.getBoundingClientRect();
        this.placement = {
            top: rect.bottom + 8,
            left: rect.left,
        };
        this.view = "days";
        this.open = true;
    },

    switchView() {
        if (this.view === "days") this.view = "months";
        else if (this.view === "months") this.view = "years";
    },

    prev() {
        if (this.view === "days") {
            if (this.month === 0) {
                this.month = 11;
                this.year--;
            } else {
                this.month--;
            }
        } else if (this.view === "months") {
            this.year--;
        } else if (this.view === "years") {
            this.year -= 10;
        }
    },

    next() {
        if (this.view === "days") {
            if (this.month === 11) {
                this.month = 0;
                this.year++;
            } else {
                this.month++;
            }
        } else if (this.view === "months") {
            this.year++;
        } else if (this.view === "years") {
            this.year += 10;
        }
    },

    selectDate(day) {
        const date = new Date(this.year, this.month, day);
        this.form[fieldName] = this.formatDate(date);
        this.open = false;

        this.$nextTick(() => document.activeElement?.blur());
    },

    selectMonth(index) {
        this.month = index;
        this.view = "days";
    },

    selectYear(y) {
        this.year = y;
        this.view = "months";
    },

    selectToday() {
        const today = new Date();
        this.month = today.getMonth();
        this.year = today.getFullYear();
        this.selectDate(today.getDate());
    },

    clear() {
        this.form[fieldName] = "";
        this.open = false;
    },

    isSelected(day) {
        const selected = this.parseDate(this.form[fieldName]);
        if (!selected) return false;

        return (
            selected.getDate() === day &&
            selected.getMonth() === this.month &&
            selected.getFullYear() === this.year
        );
    },

    isToday(day) {
        const today = new Date();
        return (
            day === today.getDate() &&
            this.month === today.getMonth() &&
            this.year === today.getFullYear()
        );
    },
});
