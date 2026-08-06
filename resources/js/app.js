import "./bootstrap";
import "flowbite";
import Alpine from "alpinejs";
import { listTable } from "./utilities/global";
import { resourceForm } from "./utilities/form";
import { datepicker } from "./utilities/datepicker";
import { dropdown } from "./utilities/dropdown";

document.addEventListener("alpine:init", () => {
    Alpine.data("listTable", listTable);
    Alpine.data("resourceForm", resourceForm);
    Alpine.data("datepicker", datepicker);
    Alpine.data("dropdown", dropdown);
});

if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}

window.toggleDarkMode = function () {
    const isDark = document.documentElement.classList.toggle("dark");
    localStorage.theme = isDark ? "dark" : "light";
};

const baseUri = (slug = "students") => `/api/v1/${slug}`;
const getById = (hashtag = "") => document.getElementById(hashtag);
const apiFetch = async (slug = "students", options = {}) => {
    const type = "application/json";
    const res = await fetch(baseUri(slug), {
        ...options,
        headers: {
            "Content-Type": type,
            Accept: type,
        },
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    return res.json();
};

const toList = (fields, dom, has_id = false) => {
    Object.entries(fields).forEach(([field, value]) => {
        const el = dom.form.querySelector(`[name="${field}"]`);
        if (el) el.value = has_id ? value.id : (value ?? "");
    });
};

export { getById, apiFetch, baseUri, toList };
