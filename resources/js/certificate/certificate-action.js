// certificate-actions.js
import { CONFIG } from './config.js';
import { Toast } from './core.js';

/**
 * Handles editing an existing certificate.
 * Fetches certificate details by ID and opens the StudentStatusModal in EDIT mode.
 */
export async function handleEditAction(ApiService, certificateId) {
    // 1. Fetch certificate details from API
    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${certificateId}`);

    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យបានទេ' });
        return;
    }

    const cert = data.data || data;

    // 2. Open modal passing both student object and certificate object
    window.StudentStatusModal.open({
        id: cert.student?.id,

    nameKh: `${cert.student?.person?.first_name_kh || ''} ${cert.student?.person?.last_name_kh || ''}`.trim() || 'គ្មានទិន្នន័យ',
    nameEn: `${cert.student?.person?.last_name || ''} ${cert.student?.person?.first_name || ''}`.trim() || 'N/A',

        sex: cert.student?.person?.gender || cert.student?.gender,
        studentCode: cert.student?.code,
        dob: cert.student?.person?.dob || cert.student?.dob,
        major: cert.student?.major?.name
    }, cert); // Pass `cert` as 2nd parameter to activate Edit mode
}

/**
 * Confirms and deletes/revokes an issued certificate.
 */
export async function handleDeleteAction(ApiService, certificateId, onSuccessCallback) {
    const confirmation = await Swal.fire({
        title: 'តើអ្នកប្រាកដជាចង់លុបលិខិតនេះមែនទេ?', // Are you sure you want to delete this certificate?
        text: 'ទិន្នន័យនេះមិនអាចយកមកវិញបានឡើយ!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'បាទ/ចាស លុបវា!',
        cancelButtonText: 'បោះបង់',
    });

    if (!confirmation.isConfirmed) return;

    const { error } = await ApiService.request(`${CONFIG.API_BASE}/${certificateId}`, {
        method: 'DELETE'
    });

    if (!error) {
        Toast.fire({ icon: 'success', title: 'លុបលិខិតបានជោគជ័យ!' });
        if (typeof onSuccessCallback === 'function') {
            onSuccessCallback(); // Refresh table
        }
    } else {
        Toast.fire({ icon: 'error', title: 'មានបញ្ហាមិនអាចលុបទិន្នន័យនេះបាន' });
    }
}
