import axios from "axios";

const _type = "application/json";
const apiCRUD = axios.create({
    baseURL: "/api/v1",
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: _type,
        "Content-Type": _type,
    },
});

// CSRF token — Laravel needs this on every mutating request
const _token = document.querySelector<HTMLMetaElement>(
    'meta[name="csrf-token"]',
);
if (_token) apiCRUD.defaults.headers.common["X-CSRF-TOKEN"] = _token.content;

// Optional: centralize error handling (401 → redirect to login, etc.)
apiCRUD.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) window.location.href = "/login";

        return Promise.reject(error);
    },
);

export default apiCRUD;
