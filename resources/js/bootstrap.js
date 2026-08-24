import axios from "axios";

import axios from "axios";

window.axios = axios;

const { headers } = axios.defaults;
export const type = "application/json";
headers.common["X-Requested-With"] = "XMLHttpRequest";
headers.common["Accept"] = type;
headers.common["Content-Type"] = type;

const token = document.head.querySelector('meta[name="csrf-token"]');
token
    ? (headers.common["X-CSRF-TOKEN"] = token.content)
    : console.error(
        'CSRF token not found: add <meta name="csrf-token" content="{{ csrf_token() }}"> to your layout <head>.',
    );
