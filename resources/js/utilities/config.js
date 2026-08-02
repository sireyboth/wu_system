import axios from "axios";
import { type } from "../bootstrap";

const apiCRUD = axios.create({
    baseURL: "/api/v1",
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: type,
        "Content-Type": type,
    },
});

// Optional: centralize error handling (401 → redirect to login, etc.)
apiCRUD.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) window.location.href = "/login";

        return Promise.reject(error);
    },
);

export default apiCRUD;
