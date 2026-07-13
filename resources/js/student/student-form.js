import { CONFIG } from "./config.js";
import { state, Toast } from "./core.js";
import { parseNestedFormData } from "./form-utils.js";
import { renderTable } from "./table-render.js";
import { toggleModal, switchTab } from "./ui.js";
import { populateAddressCascade } from "./address-cascade.js";
import { getById, toList } from "../app.js";

/**
 * Loads the student list (optionally filtered by search) and renders it.
 * Cancels any still-in-flight previous search so a slow response can't
 * overwrite the table with stale results after a faster, newer one lands.
 *
 * NOTE: no page/per_page params yet — add these once the backend paginates
 * (CONFIG.PER_PAGE is already there for it). See code review notes.
 */
export async function loadStudents(dom, ApiService, searchQuery = "") {
    state.searchAbortController?.abort();
    state.searchAbortController = new AbortController();

    const url = `${CONFIG.API_BASE}?search=${encodeURIComponent(searchQuery)}`;
    const { error, aborted, data } = await ApiService.request(url, {
        signal: state.searchAbortController.signal,
    });

    if (aborted) return; // superseded by a newer search — do nothing
    if (error) {
        Toast.fire({ icon: "error", title: "Failed to load students" });
        return;
    }

    const records =
        data && Array.isArray(data.data)
            ? data.data
            : Array.isArray(data)
              ? data
              : [];
    renderTable(dom, records);
}

/**
 * Loads one student's full record into the form and opens the edit modal.
 */
export async function handleEditAction(dom, ApiService, id) {
    const { error, data } = await ApiService.request(
        `${CONFIG.API_BASE}/${id}`,
    );
    if (error) {
        Toast.fire({ icon: "error", title: "មិនអាចទាញយកទិន្នន័យបានទេ" });
        return;
    }

    const payload = data.data || data;
    const { person, batch, status, group, shift, major, guardians } = payload;
    const { addresses, nationality } = payload.person;

    state.isEditMode = true;
    state.editingStudentId = id;

    if (dom.modalTitle)
        dom.modalTitle.textContent =
            "កែប្រែព័ត៌មានលម្អិតនិស្សិត (Update Student Profile)";
    if (dom.submitBtn)
        dom.submitBtn.textContent = "ធ្វើបច្ចុប្បន្នភាពទិន្នន័យ (Update)";

    if (!dom.form) return;

    // 1. Academic details
    ['code', 'status', 'batch_id', 'major_id', 'nationality_id', 'shift_id','admission_at', 'bacc_2_code', 'entrance_exam', 'exit_exam', 'degree_type',
        'degree_type', 'intake', 'scholarship', 'high-school'
    ].forEach((field) => {
        const el = dom.form.querySelector(`[name="${field}"]`);
        if (el) el.value = payload[field] ?? '';
    });

    // 2. Student personal profile + address
    if (person) {
        const personFields = {
            first_name: person.first_name,
            last_name: person.last_name,
            first_name_kh: person.first_name_kh,
            last_name_kh: person.last_name_kh,
            dob: person.dob,
            sex: person.sex,
            email: person.email,
            nationality_id: person.nationality?.id,
        };
        toList(personFields, dom);

        (person.phones ?? []).forEach((phone, index) => {
            const el = dom.form.querySelector(`[name="phones[${index}]"]`);
            if (el) el.value = phone ?? "";
        });

        await populateAddressCascade(ApiService, "student_", addresses[0]);
        await populateAddressCascade(ApiService, "student-", addresses[1]);
    }

    // 3. Guardian
    if (Array.isArray(guardians) && guardians.length > 0) {
        const guard = guardians[0];

        ["name_kh", "name_en", "job", "relationship"].forEach((field) => {
            const el = dom.form.querySelector(
                `[name="guardians[0][${field}]"]`,
            );
            if (el) el.value = guard[field] ?? "";
        });

        ["phones", "addresses"].forEach((field) => {
            const values = guard[field] ?? [];
            values.forEach((value, index) => {
                const el = dom.form.querySelector(
                    `[name="guardians[0][${field}][${index}]"]`,
                );
                if (el) el.value = value ?? "";
            });
        });
    }

    switchTab("identity");
    toggleModal(dom, true); // fixed: was `AppModal(true)` — an object called as a function
}

/**
 * Confirms and deletes a student record.
 */
export async function handleDeleteAction(dom, ApiService, id) {
    const confirmation = await Swal.fire({
        title: "តើអ្នកប្រាកដជាចង់លុបមែនទេ?",
        text: "ទិន្នន័យនេះមិនអាចយកមកវិញបានឡើយ!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#ef4444",
        confirmButtonText: "បាទ/ចាស លុបវា!",
        cancelButtonText: "បោះបង់",
    });

    if (!confirmation.isConfirmed) return;

    const { error } = await ApiService.request(`${CONFIG.API_BASE}/${id}`, {
        method: "DELETE",
    });
    if (!error) {
        Toast.fire({ icon: "success", title: "លុបទិន្នន័យបានជោគជ័យ!" });
        loadStudents(dom, ApiService, dom.searchInput?.value || "");
    } else {
        Toast.fire({ icon: "error", title: "មានបញ្ហាមិនអាចលុបទិន្នន័យនេះបាន" });
    }
}

/**
 * Validates and submits the create/edit form.
 */
export async function handleFormSubmit(dom, ApiService, e) {
    e.preventDefault();
    if (!dom.form || !dom.submitBtn) return;

    dom.form.querySelectorAll("input, select, textarea").forEach((element) => {
        element.classList.remove(
            "border-rose-500",
            "ring-4",
            "ring-rose-500/20",
            "dark:border-rose-500",
        );
    });

    const invalidFields = dom.form.querySelectorAll(":invalid");
    if (invalidFields.length > 0) {
        invalidFields.forEach((field) => {
            field.classList.add(
                "border-rose-500",
                "ring-4",
                "ring-rose-500/20",
                "dark:border-rose-500",
            );
        });

        const firstInvalidField = invalidFields[0];
        const closestPanel = firstInvalidField.closest(".tab-panel");
        if (closestPanel) {
            const tabId = closestPanel.id.replace("panel-", "");
            switchTab(tabId);
        }

        Toast.fire({
            icon: "warning",
            title: "សូមបំពេញព័ត៌មានដែលចាំបាច់",
            text: "សូមពិនិត្យមើលវាលទិន្នន័យដែលបានចំណាំព័ទ្ធជុំវិញពណ៌ក្រហម។",
        });

        setTimeout(() => firstInvalidField.focus(), 150);
        return;
    }

    dom.submitBtn.disabled = true;
    const payload = parseNestedFormData(dom.form);

    const url = state.isEditMode
        ? `${CONFIG.API_BASE}/${state.editingStudentId}`
        : CONFIG.API_BASE;
    const method = state.isEditMode ? "PUT" : "POST";

    const { error, status, data } = await ApiService.request(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
    });

    if (!error) {
        Toast.fire({
            icon: "success",
            title: state.isEditMode
                ? "ធ្វើបច្ចុប្បន្នភាពជោគជ័យ!"
                : "បង្កើតគណនីនិស្សិតជោគជ័យ!",
        });
        toggleModal(dom, false);
        loadStudents(dom, ApiService);
    } else if (status === 422 && data) {
        const errorMessages = data.errors
            ? Object.values(data.errors).flat()
            : ["Validation failed"];
        Toast.fire({
            icon: "warning",
            title: "ពិនិត្យទិន្នន័យឡើងវិញ",
            html: `<div class="text-left text-xs text-rose-500 mt-1 list-disc pl-4">${errorMessages.map((msg) => `<li>${msg}</li>`).join("")}</div>`,
        });
    } else {
        // 1. Detect if the API returned a validation duplicate error string or status code
        const isDuplicate =
            data?.status === 422 ||
            data?.message?.toLowerCase().includes("duplicate") ||
            data?.message?.toLowerCase().includes("already exists");

        if (isDuplicate) {
            Toast.fire({
                icon: "warning", // Warning icon fits validation constraint issues better
                title: "ទិន្នន័យស្ទួនគ្នា (Duplicate Records)",
                text: "លេខតុបាក់ឌុប ឬអត្តសញ្ញាណនិស្សិតនេះ មានក្នុងប្រព័ន្ធរួចហើយ។ (BACC II Code or Student ID already exists.)",
            });
        } else {
            const errorMsg = data?.message || "";

            // 1. Detect precise SQLite / Database Unique Constraint Failures
            if (errorMsg.includes("students.bacc_2_code")) {
                Toast.fire({
                    icon: "warning",
                    title: "លេខតុបាក់ឌុបស្ទួនគ្នា (Duplicate BACC II Code)",
                    text: "លេខតុបាក់ឌុបនេះមានក្នុងប្រព័ន្ធរួចហើយ សូមពិនិត្យឡើងវិញ។ (This BACC II Code already exists.)",
                });
            } else if (errorMsg.includes("students.code")) {
                Toast.fire({
                    icon: "warning",
                    title: "អត្តសញ្ញាណនិស្សិតស្ទួនគ្នា (Duplicate Student ID)",
                    text: "អត្តសញ្ញាណប័ណ្ណនិស្សិត (ID Code) នេះមានក្នុងប្រព័ន្ធរួចហើយ។ (This Student ID Code already exists.)",
                });
            } else if (errorMsg.includes("UNIQUE constraint failed")) {
                Toast.fire({
                    icon: "warning",
                    title: "ទិន្នន័យស្ទួនគ្នា (Duplicate Record)",
                    text: "ព័ត៌មានខ្លះដែលអ្នកបានបញ្ចូលមានរួចរាល់នៅក្នុងប្រព័ន្ធហើយ។ (Some unique record attributes already exist.)",
                });
            } else {
                // 2. Standard Fallback for unexpected crashes
                Toast.fire({
                    icon: "error",
                    title: "មានបញ្ហាភ្ជាប់ទៅកាន់ប្រព័ន្ធ",
                    text: errorMsg || "Internal Server Error (500).",
                });
            }
        }
    }
    dom.submitBtn.disabled = false;
}
