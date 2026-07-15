const El = {
    modal:           () => document.getElementById('student-status-modal'),
    form:            () => document.getElementById('student-status-form'),
    studentIdInput:  () => document.getElementById('student_id'),
    dateEng:         () => document.getElementById('dateEng'),
    dateKh:          () => document.getElementById('dateKh'),
    shortDateKh:     () => document.getElementById('shortDateKh'),
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
        ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        bg-pink-50 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400">ស្រី</span>`
        : `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">ប្រុស</span>`;
}

function open(student) {
    const modal = El.modal();
    if (!modal) {
        console.error('[StudentStatusModal] #student-status-modal not found in DOM.');
        return;
    }

    if (student) {
        El.studentIdInput().value = student.id;
        El.infoNameKh().textContent = student.nameKh;
        El.infoNameEn().textContent = student.nameEn;
        El.infoSexBadge().innerHTML = sexBadgeHtml(student.sex);
        El.infoCode().textContent = student.studentCode || '—';
        El.infoDob().textContent = formatDob(student.dob);
        El.infoMajor().textContent = student.major || '—';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function close() {
    const modal = El.modal();
    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    El.form()?.reset();
    El.dateKh().value = '';
    El.shortDateKh().value = '';
    El.studentIdInput().value = '';
    El.infoNameKh().textContent = '—';
    El.infoNameEn().textContent = '—';
    El.infoSexBadge().innerHTML = '';
    El.infoCode().textContent = '—';
    El.infoDob().textContent = '—';
    El.infoMajor().textContent = '—';
}

function handleDateChange(e) {
    const val = e.target.value;
    if (!val) {
        El.dateKh().value = '';
        El.shortDateKh().value = '';
        return;
    }

    const [year, month, day] = val.split('-').map(Number);
    const khmer = momentkh.fromGregorian(year, month, day);

    El.dateKh().value = momentkh.format(khmer);
    El.shortDateKh().value = momentkh.format(khmer, 'Ds ខែM ឆ្នាំc');
}

function handleSubmit(e) {
    e.preventDefault();
    const formData = new FormData(El.form());

    fetch("/probisional-certificate/store", {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
    })
        .then((res) => res.json())
        .then(() => close())
        .catch((err) => console.error('[StudentStatusModal] Save failed:', err));
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

window.StudentStatusModal = { open, close };