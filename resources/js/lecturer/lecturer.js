// 1. GLOBAL UTILITIES & CONFIG
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

const form = document.getElementById('addlecturerForm');

// 2. CORE INITIALIZATION
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('lecturerSearchInput');
    let debounceTimer;

    // FIX: Changed from fetchlecturers() to fetchLecturers()
    fetchLecturers();

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchLecturers(e.target.value), 300);
        });
    }
});

// 3. API & DATA FETCHING
// resources/js/lecturer-mgt/lecturer.js

async function fetchLecturers(search = '') {
    const loader = document.getElementById('loading-overlay');
    if (loader) loader.classList.remove('hidden');

    try {
        const response = await fetch(`/api/v1/lecturers?search=${search}`);
        const result = await response.json();

        console.log("API Result:", result); // Debug tracking

        // FIX: Check if result has a valid 'data' array inside the pagination object
        if (result && Array.isArray(result.data)) {
            renderTable(result.data);
        } else {
            console.warn("Invalid data structure received:", result);
            renderTable([]);
        }
    } catch (error) {
        console.error("Fetch Error:", error);
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

    // Pass both item and array index to keep iteration counts precise
    tableBody.innerHTML = lecturers.map((lecturer, index) => generateTableRowHtml(lecturer, index)).join('');
}

// 5. COMPONENT TEMPLATES
function generateTableRowHtml(lecturer, index) {
    // 1. Date Formatting (e.g., 29 Jun 2026)
    const formattedDate = new Date(lecturer.created_at).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    return `
        <tr class="group hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 transition-all duration-200 border-b border-neutral-100 dark:border-white/5">
            <!-- Index Number -->
            <td class="px-6 py-4 text-neutral-500 font-mono text-sm">${index + 1}</td>

            <!-- Code Column -->
            <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                ${lecturer.code ?? '<span class="text-neutral-400 italic">N/A</span>'}
            </td>

            <!-- Name Details (Khmer & English combined for a clean UI layout) -->
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <!-- Identity User Icon Wrapper -->
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

            <!-- Remarks / Notes -->
            <td class="px-6 py-4">
                <p class="text-sm text-neutral-600 dark:text-neutral-400 max-w-[250px] truncate" title="${lecturer.remark ?? ''}">
                    ${lecturer.remark ?? '<span class="italic opacity-40 text-xs">No remarks</span>'}
                </p>
            </td>

            <!-- Assigned Date -->
            <td class="px-6 py-4 text-xs text-neutral-500 font-mono">${formattedDate}</td>

            <!-- CRUD Actions -->
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

// ====================== Add New Data ======================

//Open Modal
window.toggleModal = function() {
    const modal = document.getElementById('lecturerModal');
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
};

// 1. Toggle Modal Visibility
function toggleModal() {
    const modal = document.getElementById('lecturerModal'); // Ensure your modal wrapper has this ID
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex'); // Optional: fits centering flex alignments
    }
}

// 2. Intercept Form Submission
// Inside your form submit event listener...
if (form) {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        // 1. Get raw form entries
        const formData = new FormData(form);
        const rawPayload = Object.fromEntries(formData.entries());

        // 2. CLEAN THE PAYLOAD: Convert empty strings to null (Just like Postman treats omitted fields)
        const cleanPayload = {};
        for (const [key, value] of Object.entries(rawPayload)) {
            // If field is empty text, set it to null so Laravel treats it as nullable
            cleanPayload[key] = value.trim() === '' ? null : value.trim();
        }

        try {
            // 3. Send the request
            // 🔍 SPY: Print the payload to compare with Postman
            console.log("👉 PAYLOAD BEING SENT TO LARAVEL:", cleanPayload);
            const response = await fetch('/api/v1/lecturers', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    // Pass the CSRF token via header
                    // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(cleanPayload) // Send clean data
            });

            // 4. FIX THE SYNTAX ERROR: Check content type before parsing as JSON
            const contentType = response.headers.get("content-type");
            let result = {};
            if (contentType && contentType.includes("application/json")) {
                result = await response.json();
            } else {
                // If it's HTML/text, grab raw text to inspect the server crash
                const rawText = await response.text();
                console.error("Server returned non-JSON response:", rawText);
                throw new Error("Server returned a 500 internal error page.");
            }

            if (response.ok) {
                Toast.fire({
                    icon: 'success',
                    title: 'បង្កើតសាស្ត្រាចារ្យជោគជ័យ!',
                    text: `${cleanPayload.name_kh} has been added.`
                });

                form.reset();
                window.toggleModal(); // Access the global window-scoped toggle
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
            console.error("Submission Failure:", error);
            Toast.fire({
                icon: 'error',
                title: 'មានបញ្ហាភ្ជាប់ទៅកាន់ប្រព័ន្ធ',
                text: 'Internal Server Error (500). Check Laravel log files.'
            });
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
}
