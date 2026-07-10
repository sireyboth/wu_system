import { CONFIG } from './config.js';
import { fillSelectOptions } from './form-utils.js';

/**
 * Locks and clears a dependent select field back to its placeholder state.
 */
export function resetSelectField(elementId, labelKhmer) {
    const el = document.getElementById(elementId);
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
export function setupCascadeListener(ApiService, parentId, childId, apiEndpoint, labelKhmer) {
    const parentEl = document.getElementById(parentId);
    const childEl = document.getElementById(childId);
    if (!parentEl || !childEl) return;

    parentEl.addEventListener('change', async function handleChange() {
        const apiLookupId = this.value;

        childEl.innerHTML = `<option value="" disabled selected>-- ជ្រើសរើស${labelKhmer} --</option>`;
        childEl.disabled = true;

        const prefix = parentId.startsWith('student_') ? 'student_' : 'guardian_';
        if (childId === `${prefix}district`) {
            resetSelectField(`${prefix}commune`, 'ឃុំ');
            resetSelectField(`${prefix}village`, 'ភូមិ');
        } else if (childId === `${prefix}commune`) {
            resetSelectField(`${prefix}village`, 'ភូមិ');
        }

        if (!apiLookupId) return;

        const { error, data } = await ApiService.request(`${apiEndpoint}/${apiLookupId}`);
        if (!error) {
            const items = data?.data || data || [];
            fillSelectOptions(childEl, items, 'id', 'name');
            childEl.disabled = false;
        }
    });
}

/**
 * Pre-loads static reference lists (batches, majors, provinces) and wires
 * up the student + guardian cascading address selectors.
 */
export async function initFormLookups(ApiService) {
    const [batchesRes, majorsRes, provinceRes] = await Promise.all([
        ApiService.request(CONFIG.API_LOOKUPS.batches),
        ApiService.request(CONFIG.API_LOOKUPS.majors),
        ApiService.request(CONFIG.API_LOOKUPS.provinces),
    ]);

    if (!batchesRes.error) {
        fillSelectOptions(document.getElementById('academic_batch_id'), batchesRes.data?.data || batchesRes.data);
    }
    if (!majorsRes.error) {
        fillSelectOptions(document.getElementById('academic_major_id'), majorsRes.data?.data || majorsRes.data);
    }

    const provinceList = provinceRes.data?.data || provinceRes.data || [];
    fillSelectOptions(document.getElementById('student_province'), provinceList);
    fillSelectOptions(document.getElementById('guardian_province'), provinceList);

    setupCascadeListener(ApiService, 'student_province', 'student_district', CONFIG.API_LOOKUPS.districts, 'ស្រុក');
    setupCascadeListener(ApiService, 'student_district', 'student_commune', CONFIG.API_LOOKUPS.communes, 'ឃុំ');
    setupCascadeListener(ApiService, 'student_commune', 'student_village', CONFIG.API_LOOKUPS.villages, 'ភូមិ');

    setupCascadeListener(ApiService, 'guardian_province', 'guardian_district', CONFIG.API_LOOKUPS.districts, 'ស្រុក');
    setupCascadeListener(ApiService, 'guardian_district', 'guardian_commune', CONFIG.API_LOOKUPS.communes, 'ឃុំ');
    setupCascadeListener(ApiService, 'guardian_commune', 'guardian_village', CONFIG.API_LOOKUPS.villages, 'ភូមិ');
}

/**
 * Given a saved address object ({ street, province_id, district, commune, village }),
 * populates and auto-selects the full province -> village chain for either the
 * "student_" or "guardian_" prefixed selector group.
 *
 * NOTE: this does 3 sequential awaited requests (district/commune/village) because
 * each depends on the previous ID. If edit-modal load time matters, the real fix
 * is a backend endpoint that returns the full hierarchy for one address in a
 * single call — see the code review notes.
 */
export async function populateAddressCascade(ApiService, prefix, address) {
    if (!address) return;

    const streetEl = document.querySelector(
        prefix === 'student_' ? `[name="addresses[0][street]"]` : `[name="guardians[0][addresses][0][street]"]`
    );
    if (streetEl) streetEl.value = address.street ?? '';

    const provSelect = document.getElementById(`${prefix}province`);
    if (!provSelect || !address.province_id) return;
    provSelect.value = address.province_id;

    const distSelect = document.getElementById(`${prefix}district`);
    const distRes = await ApiService.request(`${CONFIG.API_LOOKUPS.districts}/${address.province_id}`);
    if (!distRes.error) {
        fillSelectOptions(distSelect, distRes.data?.data || distRes.data, 'id', 'name');
        distSelect.value = address.district ?? '';
        distSelect.disabled = false;
    }

    const commSelect = document.getElementById(`${prefix}commune`);
    const commRes = await ApiService.request(`${CONFIG.API_LOOKUPS.communes}/${address.district}`);
    if (!commRes.error) {
        fillSelectOptions(commSelect, commRes.data?.data || commRes.data, 'id', 'name');
        commSelect.value = address.commune ?? '';
        commSelect.disabled = false;
    }

    const villSelect = document.getElementById(`${prefix}village`);
    const villRes = await ApiService.request(`${CONFIG.API_LOOKUPS.villages}/${address.commune}`);
    if (!villRes.error) {
        fillSelectOptions(villSelect, villRes.data?.data || villRes.data, 'id', 'name');
        villSelect.value = address.village ?? '';
        villSelect.disabled = false;
    }
}
