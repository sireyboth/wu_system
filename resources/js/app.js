import "./bootstrap";
import "flowbite";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();
// resources/js/app.js
window.toggleDarkMode = function () {
    const isDark = document.documentElement.classList.toggle("dark");
    localStorage.theme = isDark ? "dark" : "light";
};

const getById = (hashtag = "") => document.getElementById(hashtag);
const apiFetch = async (slug = "", options = {}) => {
    const type = "application/json";
    const res = await fetch(`{/api/v1${slug}`, {
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
