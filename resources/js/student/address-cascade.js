import { getById } from "../app.js";
import { CONFIG } from "./config.js";
import { fillSelectOptions } from "./form-utils.js";

/**
 * Locks and clears a dependent select field back to its placeholder state.
 */
export function resetSelectField(elementId, labelKhmer) {
    const el = getById(elementId);
    if (el) {
        el.innerHTML = `<option value="" disabled selected>-- ជ្រើសរើស${labelKhmer} --</option>`;
        el.disabled = true;
    }
}

/**
 * Wires a parent select -> child select cascade (e.g. province -> district).
 * Also resets any further-downstream fields (commune, village) when the
 * parent changes, since they're no longer valid.
 */
export function setupCascadeListener(
    ApiService,
    parentId,
    childId,
    apiEndpoint,
    labelKhmer,
) {
    const parentEl = getById(parentId);
    const childEl = getById(childId);
    if (!parentEl || !childEl) return;

    parentEl.addEventListener("change", async function handleChange() {
        const apiLookupId = this.value;

        childEl.innerHTML = `<option value="" disabled selected>-- ជ្រើសរើស${labelKhmer} --</option>`;
        childEl.disabled = true;

        const prefix = parentId.startsWith("student_")
            ? "student_"
            : "student-";
        if (childId === `${prefix}district`) {
            resetSelectField(`${prefix}commune`, "ឃុំ");
            resetSelectField(`${prefix}village`, "ភូមិ");
        } else if (childId === `${prefix}commune`) {
            resetSelectField(`${prefix}village`, "ភូមិ");
        }

        if (!apiLookupId) return;

        const { error, data } = await ApiService.request(
            `${apiEndpoint}/${apiLookupId}`,
        );
        if (!error) {
            const items = data?.data || data || [];
            fillSelectOptions(childEl, items, "id", "name");
            childEl.disabled = false;
        }
    });
}

/**
 * Pre-loads static reference lists (batches, majors, provinces) and wires
 * up the student + guardian cascading address selectors.
 */
export async function initFormLookups(ApiService) {
    const [batchesRes, majorsRes, provinceRes, nationalityRes, groupRes, shiftRes, statusRes] =
        await Promise.all([
            ApiService.request(CONFIG.API_LOOKUPS.batches),
            ApiService.request(CONFIG.API_LOOKUPS.majors),
            ApiService.request(CONFIG.API_LOOKUPS.provinces),
            ApiService.request(CONFIG.API_LOOKUPS.nationalities),
            ApiService.request(CONFIG.API_LOOKUPS.groups),
            ApiService.request(CONFIG.API_LOOKUPS.shifts),
            ApiService.request(CONFIG.API_LOOKUPS.statuses),
        ]);

    if (!batchesRes.error) {
        fillSelectOptions(
            getById("academic_batch_id"),
            batchesRes.data?.data || batchesRes.data,
        );
    }
    if (!majorsRes.error) {
        fillSelectOptions(
            getById("academic_major_id"),
            majorsRes.data?.data || majorsRes.data,
        );
    }
    if (!nationalityRes.error) {
        fillSelectOptions(
            getById("student_nationality"),
            nationalityRes.data?.data || nationalityRes.data,
        );
    }
    if (!nationalityRes.error) {
        fillSelectOptions(
            getById("student-nationality"),
            nationalityRes.data?.data || nationalityRes.data,
        );
    }
    if (!groupRes.error) {
        fillSelectOptions(
            getById("student-group"),
            groupRes.data?.data ?? groupRes.data,
        );
    }
    if (!shiftRes.error) {
        fillSelectOptions(
            getById("student-shift"),
            shiftRes.data?.data ?? shiftRes.data,
        );
    }
    if (!statusRes.error) {
        fillSelectOptions(
            getById("student-status"),
            statusRes.data?.data ?? statusRes.data,
        );
    }

    const provinceList = provinceRes.data?.data || provinceRes.data || [];
    fillSelectOptions(getById("student_province"), provinceList);
    fillSelectOptions(getById("student-province"), provinceList);

    setupCascadeListener(
        ApiService,
        "student_province",
        "student_district",
        CONFIG.API_LOOKUPS.districts,
        "ស្រុក",
    );
    setupCascadeListener(
        ApiService,
        "student_district",
        "student_commune",
        CONFIG.API_LOOKUPS.communes,
        "ឃុំ",
    );
    setupCascadeListener(
        ApiService,
        "student_commune",
        "student_village",
        CONFIG.API_LOOKUPS.villages,
        "ភូមិ",
    );

    setupCascadeListener(
        ApiService,
        "student-province",
        "student-district",
        CONFIG.API_LOOKUPS.districts,
        "ស្រុក",
    );
    setupCascadeListener(
        ApiService,
        "student-district",
        "student-commune",
        CONFIG.API_LOOKUPS.communes,
        "ឃុំ",
    );
    setupCascadeListener(
        ApiService,
        "student-commune",
        "student-village",
        CONFIG.API_LOOKUPS.villages,
        "ភូមិ",
    );
}

/**
 * Given a saved address object ({ street, province_id, district, commune, village }),
 * populates and auto-selects the full province -> village chain for either the
 * "student_" or "student-" prefixed selector group.
 *
 * NOTE: this does 3 sequential awaited requests (district/commune/village) because
 * each depends on the previous ID. If edit-modal load time matters, the real fix
 * is a backend endpoint that returns the full hierarchy for one address in a
 * single call — see the code review notes.
 */
export async function populateAddressCascade(ApiService, prefix, address) {
    if (!address) return;

    const streetEl = document.querySelector(`[name="addresses[0][street]"]`);
    if (streetEl) streetEl.value = address.street ?? "";

    const provSelect = getById(`${prefix}province`);
    if (!provSelect || !address.province) return;
    provSelect.value = address.province?.id;

    const distSelect = getById(`${prefix}district`);
    const distRes = await ApiService.request(
        `${CONFIG.API_LOOKUPS.districts}/${address.province?.id}`,
    );
    if (!distRes.error) {
        fillSelectOptions(
            distSelect,
            distRes.data?.data || distRes.data,
            "id",
            "name",
        );
        distSelect.value = address.district?.id ?? "";
        distSelect.disabled = false;
    }

    const commSelect = getById(`${prefix}commune`);
    const commRes = await ApiService.request(
        `${CONFIG.API_LOOKUPS.communes}/${address.district.id}`,
    );
    if (!commRes.error) {
        fillSelectOptions(
            commSelect,
            commRes.data?.data || commRes.data,
            "id",
            "name",
        );
        commSelect.value = address.commune?.id ?? "";
        commSelect.disabled = false;
    }

    const villSelect = getById(`${prefix}village`);
    const villRes = await ApiService.request(
        `${CONFIG.API_LOOKUPS.villages}/${address.commune?.id}`,
    );
    if (!villRes.error) {
        fillSelectOptions(
            villSelect,
            villRes.data?.data || villRes.data,
            "id",
            "name",
        );
        villSelect.value = address.village?.id ?? "";
        villSelect.disabled = false;
    }
}
