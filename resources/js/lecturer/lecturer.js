// 1. GLOBAL UTILITIES & CONFIG
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

const form = document.getElementById('addlecturerForm');

// State managers for managing editing lifecycles
let isEditMode = false;
let editingLecturerId = null;

// 2. CORE INITIALIZATION
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('lecturerSearchInput');
    let debounceTimer;

    // Initial load when page opens
    fetchLecturers();

    // Event Listeners
    if (searchInput) {
        //console.log("🔍 Search input detected successfully!");

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value;
            //console.log(`⌨️ User typing search query: "${query}"`);

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchLecturers(query);
            }, 300); // Wait 300ms after user stops typing before hitting API
        });
    } else {
        //console.error("❌ Error: Could not find HTML input with ID 'lecturerSearchInput'!");
    }
});

// 3. API & DATA FETCHING
async function fetchLecturers(search = '') {
    const loader = document.getElementById('loading-overlay');
    if (loader) loader.classList.remove('hidden');

    try {
        // Encode the search string safely to avoid breaking URLs with spaces or special chars
        const response = await fetch(`/api/v1/lecturers?search=${encodeURIComponent(search)}`);
        const result = await response.json();

        //console.log("🔍 Search/Fetch API Result:", result);

        // Smart fallback: Check if the data lives inside a pagination wrapper (.data)
        // OR if the raw response itself is an array of records
        if (result && Array.isArray(result.data)) {
            renderTable(result.data);
        } else if (Array.isArray(result)) {
            renderTable(result);
        } else {
            //console.warn("Unexpected structural layout format returned:", result);
            renderTable([]);
        }
    } catch (error) {
        //console.error("Fetch Error:", error);
        if (typeof Toast !== 'undefined') {
            Toast.fire({ icon: 'error', title: 'Failed to load lecturers' });
        }
    } finally {
        if (loader) loader.classList.add('hidden');
    }
}

// 4. UI RENDERING LOGIC
function renderTable(lecturers) {
    const tableBody = document.getElementById('lecturer-table-body');
    if (!tableBody) return;

    if (!lecturers || lecturers.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-10 text-neutral-500">No records found.</td></tr>';
        return;
    }

    tableBody.innerHTML = lecturers.map((lecturer, index) => generateTableRowHtml(lecturer, index)).join('');
}

// 5. COMPONENT TEMPLATES
function generateTableRowHtml(lecturer, index) {
    const formattedDate = new Date(lecturer.created_at).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    return `
        <tr class="group hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 transition-all duration-200 border-b border-neutral-100 dark:border-white/5">
            <td class="px-6 py-4 text-neutral-500 font-mono text-sm">${index + 1}</td>
            <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                ${lecturer.code ?? '<span class="text-neutral-400 italic">N/A</span>'}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-neutral-900 dark:text-white">${lecturer.name_kh}</div>
                        <div class="text-xs text-neutral-400 font-mono">${lecturer.name_en}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <p class="text-sm text-neutral-600 dark:text-neutral-400 max-w-[250px] truncate" title="${lecturer.remark ?? ''}">
                    ${lecturer.remark ?? '<span class="italic opacity-40 text-xs">No remarks</span>'}
                </p>
            </td>
            <td class="px-6 py-4 text-xs text-neutral-500 font-mono">${formattedDate}</td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                    <button onclick="editLecturer(${lecturer.id})" class="p-2 text-amber-600 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg transition-colors" title="Edit Lecturer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button onclick="deleteLecturer(${lecturer.id})" class="p-2 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition-colors" title="Delete Lecturer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

// ====================== Add / Edit / Delete Data Operations ======================

// Open & Close Modal Controller
window.toggleModal = function() {
    const modal = document.getElementById('lecturerModal');
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }

    // Reset back to creation mode if modal is closed down manually
    if (modal && modal.classList.contains('hidden')) {
        resetFormState();
    }
};

// Reset State clean helper
function resetFormState() {
    if (form) form.reset();
    isEditMode = false;
    editingLecturerId = null;

    // Switch modal title text back to normal default language structures
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.textContent = 'បន្ថែមសាស្ត្រាចារ្យថ្មី'; // "Add New Lecturer"

    const submitBtn = form?.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.textContent = 'រក្សាទុក'; // "Save"
}

// 6. EDIT OPERATION (GET TARGET DATA & POPULATE)
window.editLecturer = async function(id) {
    const loader = document.getElementById('loading-overlay');
    if (loader) loader.classList.remove('hidden');

    try {
        // Fetch specific singular record from API resource endpoint
        const response = await fetch(`/api/v1/lecturers/${id}`);
        if (!response.ok) throw new Error('Failed to retrieve record parameters');

        const result = await response.json();
        const data = result.data || result; // Cover wrapper scenarios safely

        // Set tracking variables up to shift update logic pathways
        isEditMode = true;
        editingLecturerId = id;

        // Change Title & Buttons visually to match Update Actions
        const modalTitle = document.getElementById('modalTitle');
        if (modalTitle) modalTitle.textContent = 'កែប្រែព័ត៌មានសាស្ត្រាចារ្យ'; // "Edit Lecturer Info"

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.textContent = 'ធ្វើបច្ចុប្បន្នភាព'; // "Update"

        // Autofill form inputs by matching input elements name attributes
        if (form) {
            form.querySelector('[name="name_kh"]').value = data.name_kh ?? '';
            form.querySelector('[name="name_en"]').value = data.name_en ?? '';
            form.querySelector('[name="code"]').value = data.code ?? '';
            form.querySelector('[name="remark"]').value = data.remark ?? '';
        }

        // Open Modal interface
        window.toggleModal();

    } catch (error) {
        //console.error("Edit Selection Failure:", error);
        Toast.fire({ icon: 'error', title: 'មិនអាចទាញយកទិន្នន័យបានទេ' });
    } finally {
        if (loader) loader.classList.add('hidden');
    }
};

// 7. DELETE OPERATION (PROMPT & PURGE)
window.deleteLecturer = async function(id) {
    // Elegant system validation check using SweetAlert confirmation layout structures
    const confirmation = await Swal.fire({
        title: 'តើអ្នកប្រាកដជាចង់លុបមែនទេ?',
        text: "ទិន្នន័យនេះមិនអាចយកមកវិញបានឡើយ!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5', // Indigo-600 styling highlight
        cancelButtonColor: '#ef4444',  // Rose-500
        confirmButtonText: 'បាទ/ចាស លុបវា!',
        cancelButtonText: 'បោះបង់'
    });

    if (!confirmation.isConfirmed) return;

    const loader = document.getElementById('loading-overlay');
    if (loader) loader.classList.remove('hidden');

    try {
        const response = await fetch(`/api/v1/lecturers/${id}`, {
            method: 'DELETE',
            credentials: 'omit',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            Toast.fire({
                icon: 'success',
                title: 'លុបទិន្នន័យបានជោគជ័យ!'
            });
            fetchLecturers(); // Live re-render list updates
        } else {
            throw new Error('Failed to delete records resource element');
        }
    } catch (error) {
        //console.error("Delete Action Failure:", error);
        Toast.fire({ icon: 'error', title: 'មានបញ្ហាមិនអាចលុបទិន្នន័យនេះបាន' });
    } finally {
        if (loader) loader.classList.add('hidden');
    }
};

// 8. INTERCEPT & DISPATCH FORM ENTRIES (POST / PUT)
if (form) {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        const formData = new FormData(form);
        const rawPayload = Object.fromEntries(formData.entries());

        const cleanPayload = {};
        for (const [key, value] of Object.entries(rawPayload)) {
            // 🚫 FIX: If the key is 'search', skip it entirely! Do not send it to the store/update API
            if (key === 'search') continue;

            cleanPayload[key] = value.trim() === '' ? null : value.trim();
        }

        try {
            const url = isEditMode ? `/api/v1/lecturers/${editingLecturerId}` : '/api/v1/lecturers';
            const method = isEditMode ? 'PUT' : 'POST';

            //console.log(`👉 DISPATCHING REQUEST [${method}] Target: ${url}`, cleanPayload);

            const response = await fetch(url, {
                method: method,
                credentials: 'omit',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(cleanPayload)
            });

            const contentType = response.headers.get("content-type");
            let result = {};
            if (contentType && contentType.includes("application/json")) {
                result = await response.json();
            } else {
                const rawText = await response.text();
                //console.error("Server returned non-JSON response:", rawText);
                throw new Error("Server returned a 500 internal error page.");
            }

            if (response.ok) {
                Toast.fire({
                    icon: 'success',
                    title: isEditMode ? 'ធ្វើបច្ចុប្បន្នភាពជោគជ័យ!' : 'បង្កើតសាស្ត្រាចារ្យជោគជ័យ!',
                    text: isEditMode ? 'ព័ត៌មានត្រូវបានកែប្រែរួចរាល់។' : `${cleanPayload.name_kh} has been added.`
                });

                resetFormState();
                window.toggleModal();
                fetchLecturers();
            } else if (response.status === 422) {
                let errorMessages = result.errors ? Object.values(result.errors).flat() : ['Validation failed'];
                Toast.fire({
                    icon: 'warning',
                    title: 'ពិនិត្យទិន្នន័យឡើងវិញ',
                    html: `<div class="text-left text-xs text-rose-500 mt-1 list-disc pl-4">${errorMessages.map(msg => `<li>${msg}</li>`).join('')}</div>`
                });
            } else {
                throw new Error(result.message || 'Server error');
            }

        } catch (error) {
            //console.error("Submission Failure:", error);
            Toast.fire({
                icon: 'error',
                title: 'មានបញ្ហាភ្ជាប់ទៅកាន់ប្រព័ន្ធ',
                text: 'Internal Server Error (500). Please trace system execution logs.'
            });
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
}
