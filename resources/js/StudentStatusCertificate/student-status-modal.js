import {Toast} from './core.js';
// student-status-modal.js

const El = {
    modal:           () => document.getElementById('student-status-modal'),
    form:            () => document.getElementById('student-status-form'),
    studentIdInput:  () => document.getElementById('student_id'),
    dateEng:         () => document.getElementById('issue_date'),
    dateKh:          () => document.getElementById('full_date_kh'),
    shortDateKh:     () => document.getElementById('short_date_kh'),
    certificateNo:   () => document.getElementById('certificate_no'),
    cerNoHint:       () => document.getElementById('cerNoHint'),
    type:            () => document.getElementById('type'),
    infoNameKh:      () => document.getElementById('infoNameKh'),
    infoNameEn:      () => document.getElementById('infoNameEn'),
    infoSexBadge:    () => document.getElementById('infoSexBadge'),
    infoCode:        () => document.getElementById('infoCode'),
    infoDob:         () => document.getElementById('infoDob'),
    infoMajor:       () => document.getElementById('infoMajor'),
};

function formatDob(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric',
    });
}

function sexBadgeHtml(sex) {
    const isFemale = ['F', 'Female', 'ស្រី'].includes(sex);
    return isFemale
        ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-50 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400">ស្រី</span>`
        : `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">ប្រុស</span>`;
}

/**
 * Single 'open' declaration supporting optional certificate pre-fill for Edit mode
 */
function open(student, cert = null) {
    const modal = El.modal();
    if (!modal) {
        console.error('[StudentStatusModal] #student-status-modal not found in DOM.');
        return;
    }

    if (student) {
        if (El.studentIdInput()) El.studentIdInput().value = student.id || cert?.student_id || '';
        if (El.infoNameKh()) El.infoNameKh().textContent = student.nameKh || student.name_kh || '—';
        if (El.infoNameEn()) El.infoNameEn().textContent = student.nameEn || student.name_en || '—';
        if (El.infoSexBadge()) El.infoSexBadge().innerHTML = sexBadgeHtml(student.sex || student.gender);
        if (El.infoCode()) El.infoCode().textContent = student.studentCode || student.code || '—';
        if (El.infoDob()) El.infoDob().textContent = formatDob(student.dob);
        if (El.infoMajor()) El.infoMajor().textContent = student.major || '—';
    }

    // If editing an existing certificate, pre-fill certificate date
    if (cert && El.dateEng()) {
        El.dateEng().value = cert.issue_date || cert.date_eng || '';
        El.dateEng().dispatchEvent(new Event('change'));
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

/**
 * Single 'close' declaration
 */
function close() {
    const modal = El.modal();
    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    El.form()?.reset();
    if (El.dateKh()) El.dateKh().value = '';
    if (El.shortDateKh()) El.shortDateKh().value = '';
    if (El.studentIdInput()) El.studentIdInput().value = '';
    if (El.infoNameKh()) El.infoNameKh().textContent = '—';
    if (El.infoNameEn()) El.infoNameEn().textContent = '—';
    if (El.infoSexBadge()) El.infoSexBadge().innerHTML = '';
    if (El.infoCode()) El.infoCode().textContent = '—';
    if (El.infoDob()) El.infoDob().textContent = '—';
    if (El.infoMajor()) El.infoMajor().textContent = '—';
}

function handleDateChange(e) {
    const val = e.target.value;
    if (!val) {
        if (El.dateKh()) El.dateKh().value = '';
        if (El.shortDateKh()) El.shortDateKh().value = '';
        if (El.certificateNo()) El.certificateNo().value = '';
        if (El.cerNoHint()) El.cerNoHint().style.display = 'none';
        return;
    }

    const [year, month, day] = val.split('-').map(Number);
    const khmer = momentkh.fromGregorian(year, month, day);

    if (El.dateKh()) El.dateKh().value = momentkh.format(khmer);
    if (El.shortDateKh()) El.shortDateKh().value = momentkh.format(khmer, 'Ds ខែM ឆ្នាំc');

    fetchPreviewNumber(val);
}

function fetchPreviewNumber(issueDate) {
    const type = El.type()?.value || 'status';

    fetch(`/api/v1/certificates/preview-number?type=${type}&issue_date=${issueDate}`)
        .then((res) => res.json())
        .then((data) => {
            if (El.certificateNo()) El.certificateNo().value = data.certificate_no;
            if (El.cerNoHint()) El.cerNoHint().style.display = 'block';
        })
        .catch((err) => console.error('[StudentStatusModal] Preview fetch failed:', err));
}

let isSubmitting = false;

function handleSubmit(e) {
    e.preventDefault();
    if (isSubmitting) return;
    isSubmitting = true;

    const submitBtn = El.form()?.querySelector('[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const formData = new FormData(El.form());

    fetch("/api/v1/certificates", {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
    })
        .then((res) => {
            if (!res.ok) {
                return res.json().catch(() => ({})).then((body) => {
                    const firstError = body.errors ? Object.values(body.errors)[0]?.[0] : null;
                    throw new Error(firstError || body.message || `Request failed (${res.status})`);
                });
            }
            return res.json();
        })
        .then((data) => {
            // Trigger success toast
            Toast.fire({
                icon: 'success',
                title: data?.message || 'សញ្ញាបត្របណ្ដោះអាសន្នបានបង្កើត'
            });

            close();
            if (typeof window.reloadCertificateTable === 'function') {
                window.reloadCertificateTable();
            }
        })
        .catch((err) => {
            console.error('[StudentStatusModal] Save failed:', err);

            // Trigger error toast
            Toast.fire({
                icon: 'error',
                title: err.message || 'សញ្ញាបត្របណ្ដោះអាសន្នបរាជ័យ'
            });
        })
        .finally(() => {
            isSubmitting = false;
            if (submitBtn) submitBtn.disabled = false;
        });
}


function bindEvents() {
    El.dateEng()?.addEventListener('change', handleDateChange);
    El.form()?.addEventListener('submit', handleSubmit);

    document.getElementById('closeStudentStatusModalBtn')
        ?.addEventListener('click', close);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindEvents);
} else {
    bindEvents();
}

// Global Export
window.StudentStatusModal = { open, close };

// Compat shim for the modal's inline onclick="closeStudentStatusModal()" markup.
window.closeStudentStatusModal = close;
