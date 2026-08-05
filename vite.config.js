import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { globSync } from "glob";

const pageEntries = globSync("resources/js/**/*.js");

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                ...pageEntries,
            ],
            refresh: true,
        }),
    ],
});
