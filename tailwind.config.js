import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import flowbitePlugin from "flowbite/plugin";
import bladeForms from "./vendor/distortedfusion/blade-forms/tailwind.config.preset";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    presets: [bladeForms],
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/**/*.js",
        "./resources/**/*.ts",
        "./node_modules/flowbite/**/*.js",
        "./vendor/ddfsn/blade-components/resources/**/*.blade.php",
        "./vendor/distortedfusion/blade-forms/resources/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Kantumruy Pro"', "system-ui", "sans-serif"],
            },
        },
    },

    plugins: [require("flowbite/plugin")],
};
