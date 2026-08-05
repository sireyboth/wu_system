import { CONFIG } from './config.js';
import { Toast } from './core.js';

function formatDobKh(iso) {
    if (!iso) return '—';
    if (typeof momentkh !== 'undefined') {
        const [y, m, d] = iso.split('-').map(Number);
        return momentkh.format(momentkh.fromGregorian(y, m, d), 'Ds ខែM ឆ្នាំc');
    }
    return iso;
}

function formatDateEn(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
}

function set(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? '—';
}

/**
 * Fills the hidden #certificatePrintArea (StudentStatusCertificate/partials/certificate-print.blade.php)
 * with this certificate's data.
 */
function fillPrintArea(cert, student) {
    const person = student.person || {};
    const major = student.major || {};
    const address = Array.isArray(person.addresses) && person.addresses.length ? person.addresses[0] : null;

    const nameKh = [person.first_name_kh, person.last_name_kh].filter(Boolean).join(' ') || '—';
    const nameEn = [person.last_name, person.first_name].filter(Boolean).join(' ').toUpperCase() || '—';

    set('printDocNumber', `លេខ: ${cert.certificate_no ?? '—'}`);
    set('printNameKh', nameKh);
    set('printSexKh', person.gender === 'female' ? 'ស្រី' : 'ប្រុស');
    set('printCodeKh', student.code);
    set('printNationalityKh', person.nationality?.name_kh ?? 'ខ្មែរ');
    set('printDobKh', formatDobKh(person.dob));
    set('printAddressKh', address?.province?.name_kh);
    set('printDegreeKh', student.degree_type);
    set('printMajorKh', major.name_kh ?? major.name);

    set('printNameEn', nameEn);
    set('printSexEn', person.gender === 'female' ? 'Female' : 'Male');
    set('printCodeEn', student.code);
    set('printNationalityEn', person.nationality?.name_en ?? 'Khmer');
    set('printDobEn', formatDateEn(person.dob));
    set('printAddressEn', address?.province?.name_en);
    set('printDegreeEn', student.degree_type);
    set('printMajorEn', major.name ?? major.name_kh);

    set('printFullDateKh', cert.full_date_kh);
    set('printIssueDateEn', `Phnom Penh, ${formatDateEn(cert.issue_date)}`);
}

/**
 * Fetches a certificate, lets the user review + confirm, prints it in-page
 * (no popup — avoids the browser freeze that window.open()/document.write triggers),
 * then marks it as printed.
 */
export async function handlePrintAction(ApiService, id) {
    const { error, data } = await ApiService.request(`${CONFIG.API_BASE}/${id}`);
    if (error) {
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យសម្រាប់វិញ្ញាបនបត្រ' });
        return;
    }

    const cert = data.data || data;
    const student = cert.student || {};
    fillPrintArea(cert, student);

    const { isConfirmed } = await Swal.fire({
        title: 'តើអ្នកប្រាកដជាចង់បោះពុម្ពមែនទេ?',
        text: 'បន្ទាប់ពីបោះពុម្ព ស្ថានភាពនឹងប្តូរទៅ "បានបោះពុម្ព"',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'បោះពុម្ព (Print)',
        cancelButtonText: 'បោះបង់',
    });

    if (!isConfirmed) return;

    const printArea = document.getElementById('certificatePrintArea');
    // Move to a direct child of <body> so it isn't clipped by the dashboard
    // layout's scroll/overflow container when print positions it absolutely.
    document.body.appendChild(printArea);
    printArea.classList.remove('hidden');

    // window.print() gives JS no way to know if the user actually printed or
    // hit Cancel, so ask explicitly once the print dialog has closed instead
    // of assuming success.
    window.addEventListener('afterprint', async () => {
        printArea.classList.add('hidden');

        const { isConfirmed: printedOk } = await Swal.fire({
            title: 'តើបានបោះពុម្ពជោគជ័យទេ?',
            text: 'ចុច "បាទ/ចាស" ដើម្បីធ្វើបច្ចុប្បន្នភាពស្ថានភាពទៅ "បានបោះពុម្ព"',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'បាទ/ចាស',
            cancelButtonText: 'ទេ / បោះបង់',
        });

        if (!printedOk) return;

        const { error: markError } = await ApiService.request(`${CONFIG.API_BASE}/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                student_id: student.id,
                issue_date: cert.issue_date,
                full_date_kh: cert.full_date_kh,
                short_date_kh: cert.short_date_kh,
                type: cert.type,
                remark: cert.remark,
                status: 'printed',
            }),
        });

        if (markError) {
            Toast.fire({ icon: 'warning', title: 'ធ្វើបច្ចុប្បន្នភាពស្ថានភាពមិនបានទេ' });
        } else if (typeof window.reloadCertificateTable === 'function') {
            window.reloadCertificateTable();
        }
    }, { once: true });

    window.print();
}
