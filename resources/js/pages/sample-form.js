import { iform } from "../utils/form";

export const sampleForm = (options = {}) => ({
    ...iform({ ...options }),
    getEmptyForm() {
        const year = new Date().getFullYear();
        const semester = 1;

        return {
            year,
            semester,
            start: "",
            name: "",
            end: "",
            code: `S${semester}-${year}`, // auto-generated
            active: false,
            remark: "",
        };
    },
    updateCode() {
        const { year, semester } = this.form;
        if (year && semester) {
            this.form.code = `S${semester}-${year}`;
        }
    },
});
