/**
 * ROOM MANAGEMENT MODULE
 */

// 1. GLOBAL UTILITIES & CONFIG
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

// Make toggleModal global so HTML buttons can see it
window.toggleModal = function() {
    const modal = document.getElementById('addRoomModal');
    const form = document.getElementById('addRoomForm');
    
    if (!modal) return;

    // If we are CLOSING the modal, reset the form and the titles
    if (!modal.classList.contains("hidden")) {
        form.reset();
        form.querySelector('#room_id').value = "";
        modal.querySelector('h2').innerText = 'Add New Room';
        modal.querySelector('button[type="submit"]').innerText = 'Create Room';
    }

    modal.classList.toggle("hidden");
    modal.classList.toggle("flex");
};  

// 2. CORE INITIALIZATION
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('roomSearchInput');
    const addForm = document.getElementById('addRoomForm');
    let debounceTimer;

    // Initial Load
    fetchRooms();

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchRooms(e.target.value), 300);
        });
    }

    if (addForm) {
        addForm.addEventListener('submit', handleFormSubmit);
    }
});

let currentPage = 1;
// 3. API & DATA FETCHING
async function fetchRooms(search = '', page = 1) {
   const loader = document.getElementById('loading-overlay');
    if (loader) loader.classList.remove('hidden');

    try {
        const response = await fetch(`/api/roomsmgt?search=${search}&page=${page}`);
        const result = await response.json();
        
        renderTable(result.data);
        renderPagination(result); // Add this
    } catch (error) {
        Toast.fire({ icon: 'error', title: 'Failed to load rooms' });
    } finally {
        if (loader) loader.classList.add('hidden');
    }
}

function renderPagination(paginationData) {
    const container = document.getElementById('pagination-container');
    if (!container) return;

    // Build the page numbers list (e.g., [1, 2, 3])
    let pageNumbers = '';
    for (let i = 1; i <= paginationData.last_page; i++) {
        const isActive = i === paginationData.current_page;
        pageNumbers += `
            <button onclick="changePage(${i})" 
                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-semibold transition-all ${isActive 
                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' 
                    : 'text-neutral-600 hover:bg-indigo-50 hover:text-indigo-600'}">
                ${i}
            </button>`;
    }

    container.innerHTML = `
        <div class="flex items-center justify-between px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
            <span class="text-xs text-neutral-500">
                Showing <span class="font-bold text-neutral-900 dark:text-white">${paginationData.from || 0}</span> to 
                <span class="font-bold text-neutral-900 dark:text-white">${paginationData.to || 0}</span> of 
                <span class="font-bold text-neutral-900 dark:text-white">${paginationData.total}</span> entries
            </span>
            
            <div class="flex items-center gap-1">
                <button onclick="changePage(${paginationData.current_page - 1})" 
                        ${!paginationData.prev_page_url ? 'disabled' : ''}
                        class="px-3 py-1 text-sm font-bold text-neutral-600 rounded-lg hover:bg-neutral-100 disabled:opacity-30">
                    Prev
                </button>
                
                <div class="flex gap-1">${pageNumbers}</div>
                
                <button onclick="changePage(${paginationData.current_page + 1})" 
                        ${!paginationData.next_page_url ? 'disabled' : ''}
                        class="px-3 py-1 text-sm font-bold text-neutral-600 rounded-lg hover:bg-neutral-100 disabled:opacity-30">
                    Next
                </button>
            </div>
        </div>
    `;
}

window.changePage = function(page) {
    const search = document.getElementById('roomSearchInput').value;
    fetchRooms(search, page);
};

// 4. UI RENDERING LOGIC
function renderTable(rooms) {
    const tableBody = document.getElementById('rooms-table-body');
    if (!tableBody) return;
    
    if (!rooms || rooms.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10">No records found.</td></tr>';
        return;
    }

    tableBody.innerHTML = rooms.map(room => generateTableRowHtml(room)).join('');
}

// 5. COMPONENT TEMPLATES (Keeps renderTable clean)
function generateTableRowHtml(room) {
    
    const statusClasses = {
        'available': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
        'occupied': 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'maintenance': 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
        'inactive': 'bg-neutral-100 text-neutral-700 dark:bg-neutral-500/10 dark:text-neutral-400'
    };

    const currentStatusClass = statusClasses[room.status.toLowerCase()] || statusClasses['inactive'];
    const formattedPrice = parseFloat(room.default_unit_price).toLocaleString(undefined, {minimumFractionDigits: 2});
    const formattedDate = new Date(room.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

    return `
        <tr class="group hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 transition-all duration-200">
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="font-semibold text-neutral-900 dark:text-white">${room.room_type}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400 font-medium">${room.room_number}</td>
            <td class="px-6 py-4"><span class="text-neutral-900 dark:text-white font-bold">$${formattedPrice}</span></td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider ${currentStatusClass}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                    ${room.status}
                </span>
            </td>
            <td class="px-6 py-4">
                <p class="text-xs text-neutral-500 dark:text-neutral-400 max-w-[150px] truncate" title="${room.note ?? ''}">
                    ${room.note ?? '<span class="italic opacity-50">No notes</span>'}
                </p>
            </td>
            <td class="px-6 py-4 text-xs text-neutral-500">${formattedDate}</td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2 transition-opacity">
                    <button onclick="editRoom(${room.id})" class="p-2 text-indigo-600 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="deleteRoom(${room.id})" class="p-2 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

// 6. FORM SUBMISSION HANDLER
async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const roomId = form.querySelector('#room_id').value;
    const method = roomId ? 'PUT' : 'POST';
    const url = roomId ? `/api/roomsmgt/${roomId}` : '/api/roomsmgt';

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            Toast.fire({ icon: 'success', title: roomId ? 'Updated!' : 'Saved!' });
            resetModal();
            fetchRooms();
        } else if (response.status === 422) {
            // --- THIS IS THE FIX ---
            // If it's a validation error, show the specific message from Laravel
            const firstError = Object.values(result.errors)[0][0]; 
            Toast.fire({ 
                icon: 'warning', 
                title: firstError || 'Room number already exists!' 
            });
        } else {
            Toast.fire({ icon: 'error', title: 'Something went wrong' });
        }
    } catch (error) {
        console.error("Submission error:", error);
        Toast.fire({ icon: 'error', title: 'Network connection failed' });
    }
}
  // 7. EDIT LOGIC
window.editRoom = async function(id) {
    
    Toast.fire({ icon: 'info', title: 'Editing your data' });
    try {
        
        const response = await fetch(`/api/roomsmgt/${id}`);
        const room = await response.json();

        const form = document.getElementById('addRoomForm');
        form.querySelector('#room_id').value = room.id;
        form.querySelector('[name="room_number"]').value = room.room_number;
        form.querySelector('[name="default_unit_price"]').value = room.default_unit_price;
        form.querySelector('[name="room_type"]').value = room.room_type;
        form.querySelector('[name="note"]').value = room.note || '';

        const radio = form.querySelector(`input[name="status"][value="${room.status.toLowerCase()}"]`);
        if (radio) radio.checked = true;

        const modal = document.getElementById('addRoomModal');
        modal.querySelector('h2').innerText = 'Edit Room';
        modal.querySelector('button[type="submit"]').innerText = 'Update Room';

        // Show the modal
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    } catch (error) {
        Toast.fire({ icon: 'error', title: 'Could not fetch data' });
    }
};

// 8. DELETE LOGIC
window.deleteRoom = async function(id) {
    const result = await Swal.fire({
        title: 'Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/api/roomsmgt/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                Toast.fire({ icon: 'success', title: 'Deleted!' });
                fetchRooms(); 
            }
        } catch (error) {
            Toast.fire({ icon: 'error', title: 'Delete failed' });
        }
    }
};

// 9. Reset Modal UI back to "Add Mode"
function resetModal() {
    const form = document.getElementById('addRoomForm');
    const modal = document.getElementById('addRoomModal');
    form.reset();
    form.querySelector('#room_id').value = ''; 
    modal.querySelector('h2').innerText = 'Add New Room';
    modal.querySelector('button[type="submit"]').innerText = 'Create Room';
    
    modal.classList.add("hidden");
    modal.classList.remove("flex");
} 
