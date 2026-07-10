import "./bootstrap";
import "flowbite";
import dataTable from "./components/data-table";
import Alpine from "alpinejs";

window.Alpine = Alpine;
Alpine.data("dataTable", dataTable);

Alpine.start();
// resources/js/app.js

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

export { getById, apiFetch };
