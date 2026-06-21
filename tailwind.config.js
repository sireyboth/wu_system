import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbitePlugin from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/**/*.js',
    './resources/**/*.ts',
    './node_modules/flowbite/**/*.js',
],

    theme: {
        extend: {
            fontFamily: {
               sans: ['"Kantumruy Pro"', 'system-ui', 'sans-serif'],
            },
        },
    },

   plugins: [
    require('flowbite/plugin')
  ],
    
};
