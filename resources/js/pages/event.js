import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import rrulePlugin from "@fullcalendar/rrule";
import { apiCrud } from "../utils/config";
import { Calendar } from "@fullcalendar/core";

export const event = (config) => ({
    calendar: null,
    calendarEvents: [],
    notifiedEventIds: new Set(),

    showModal: false,
    newTitle: "",
    pendingStart: null,
    pendingEnd: null,
    repeatFreq: "",

    init() {
        if ("Notification" in window && Notification.permission === "default")
            Notification.requestPermission();

        this.calendar = new Calendar(this.$refs.calendar, {
            plugins: [
                dayGridPlugin,
                timeGridPlugin,
                interactionPlugin,
                rrulePlugin,
            ],
            initialView: "dayGridMonth",
            selectable: true,
            editable: true,

            events: (info, successCallback, failureCallback) => {
                apiCrud
                    .get(config.endpoint)
                    .then((res) => {
                        this.calendarEvents = res.data;
                        successCallback(res.data);
                    })
                    .catch(failureCallback);
            },

            select: (info) => {
                this.pendingStart = info.startStr;
                this.pendingEnd = info.endStr;
                this.showModal = true;
                this.calendar.unselect();
            },

            eventDrop: (info) => this.updateEvent(info.event),
            eventResize: (info) => this.updateEvent(info.event),

            eventClick: (info) => {
                if (confirm(`Delete '${info.event.title}'?`)) {
                    apiCrud
                        .delete(`${config.endpoint}/${info.event.id}`)
                        .then(() => info.event.remove())
                        .catch((err) => console.error("Delete failed:", err));
                }
            },
        });

        this.calendar.render();
        setInterval(() => this.checkEventAlerts(), 15000);
    },

    saveEvent() {
        apiCrud
            .post(config.endpoint, {
                title: this.newTitle,
                start: this.pendingStart,
                end: this.pendingEnd,
                repeat_freq: this.repeatFreq || null,
            })
            .then(() => {
                this.calendar.refetchEvents();
                this.showModal = false;
                this.newTitle = "";
                this.repeatFreq = "";
            })
            .catch((err) => console.error("Save failed:", err));
    },

    updateEvent(event) {
        apiCrud
            .put(`${config.endpoint}/${event.id}`, {
                title: event.title,
                start: event.startStr,
                end: event.endStr,
            })
            .catch((err) => console.error("Update failed:", err));
    },

    checkEventAlerts() {
        const now = new Date();
        this.calendarEvents.forEach((event) => {
            const eventStart = new Date(event.start);
            const diffMs = eventStart - now;

            if (
                diffMs <= 0 &&
                diffMs > -30000 &&
                !this.notifiedEventIds.has(event.id)
            ) {
                this.notifiedEventIds.add(event.id);
                this.fireNotification(event);
            }
        });
    },

    fireNotification(event) {
        const message = `Event starting now: ${event.title}`;
        if (Notification.permission === "granted") {
            new Notification("Calendar Reminder", { body: message });
        } else {
            alert(message);
        }
    },
});
