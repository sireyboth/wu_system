import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
<<<<<<< HEAD
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/dashboard/dashboard.js",
                "resources/js/salemgt/sale-mgt.js",
                "resources/js/room-mgt/room-mgt.js",
                "resources/js/taxmgt/tax-mgt.js",
                "resources/js/students/index.js",
=======
            input: ['resources/css/app.css', 'resources/js/app.js',
                'resources/js/dashboard/dashboard.js',
                'resources/js/salemgt/sale-mgt.js',
                'resources/js/room-mgt/room-mgt.js',
                'resources/js/taxmgt/tax-mgt.js',
                'resources/js/students/index.js',
>>>>>>> f883b1a (Update Architecture JS)
            ],
            refresh: true,
        }),
    ],
});
