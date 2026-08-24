import Alpine from "alpinejs";
import { apiCrud } from "./utils/config";
import { modal } from "./components/modal";
import { dropdown } from "./components/dropdown";
import { event } from "./pages/event";
import { formatDate, getStatus, getValue } from "./utils/helper";
import { table } from "./components/table";
import { sidebar } from "./components/sidebar";
import { sampleForm } from "./pages/sample-form";
import { datepicker } from "./components/datepicker";

document.addEventListener("alpine:init", () => {
    Alpine.data("sideMenu", sidebar);
    Alpine.data("tabledata", table);
    Alpine.data("sampleForm", sampleForm);
    Alpine.data("modalPopup", modal);
    Alpine.data("eventDropdown", dropdown);
    Alpine.data("datepicker", datepicker);
    Alpine.data("fullcalendar", event);

    Alpine.magic("api", () => apiCrud);
    Alpine.magic("formatDate", () => formatDate);
    Alpine.magic("getValue", () => getValue);
    Alpine.magic("getStatus", () => getStatus);
});

if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}
