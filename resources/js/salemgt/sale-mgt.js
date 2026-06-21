/**
 * General Invoice Module
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
  const modal = document.getElementById('addSaleModal');
    const formEl = document.getElementById('addSaleForm');
    const container = document.getElementById('room-items-container');
    
    if (!modal) return;

    // If we are CLOSING the modal
    if (!modal.classList.contains("hidden")) {
        formEl.reset();
        resetFormState(); // Call the fix to unlock inputs
        
        // Cleanup cloned rows
        const clonedRows = container.querySelectorAll('.remove-room-row');
        clonedRows.forEach(button => {
            const row = button.closest('.room-item-row');
            if (row) row.remove();
        });

        roomIndex = 1;

        // Reset Master Row visuals
        const masterRow = container.querySelector('.room-item-row');
        if (masterRow) {
            masterRow.querySelector('.room-select').value = "";
            masterRow.querySelector('input[name*="[unit_price]"]').value = "0.00";
            masterRow.querySelector('input[name*="[total_price]"]').value = "0.00";
        }

        modal.querySelector('h2').innerText = 'Add New Sale';
        modal.querySelector('button[type="submit"]').innerText = 'Create Invoice';
        
        if (typeof calculateInvoice === "function") calculateInvoice();
    }

    modal.classList.toggle("hidden");
    modal.classList.toggle("flex");
};  

// 2. CORE INITIALIZATION
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('saleSearchInput');
    const addForm = document.getElementById('addSaleForm');
    const roomContainer = document.getElementById('room-items-container');
    let debounceTimer;

    // 1. Initial Data Load
    console.log("Initialization started: Fetching sales data...");
    fetchSales(); 
    fetchRoomsForSelect(); // Fill the first dropdown

    // 2. Search Logic (with Null Check)
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchSales(e.target.value);
            }, 300);
        });
    }

    // 3. Form Submission (with Null Check)
    if (addForm) {
        addForm.addEventListener('submit', handleFormSubmit);
        
        // Listen for Global inputs (Dates, Booking Price)
        addForm.addEventListener('input', (e) => {
            const globalFields = ['booking_price', 'check_in_date', 'check_out_date', 'balance_completion'];
            if (globalFields.includes(e.target.name)) {
                // If dates changed, update nights first
                if (e.target.name.includes('date')) calculateNights();
                calculateInvoice();
            }
        });
    }

    // 4. Room Row Logic (Delegated)
    // This handles both the Master Row and any Cloned Rows automatically
    if (roomContainer) {
        roomContainer.addEventListener('change', function(e) {
            // Handle Room Selection
            if (e.target.classList.contains('room-select')) {
                const row = e.target.closest('.room-item-row');
                const opt = e.target.options[e.target.selectedIndex];
                
                if (opt && opt.value) {
                    row.querySelector('input[name*="[unit_price]"]').value = opt.dataset.price || 0;
                    row.querySelector('input[name*="[room_type_display]"]').value = opt.dataset.name || '-';
                } else {
                    row.querySelector('input[name*="[unit_price]"]').value = "0.00";
                    row.querySelector('input[name*="[room_type_display]"]').value = "-";
                }
                calculateInvoice();
            }
        });

        // Handle Row Inputs (Food, Discount)
        roomContainer.addEventListener('input', function(e) {
            if (e.target.name.includes('food_price') || e.target.name.includes('discount')) {
                calculateInvoice();
            }
        });
    }
});

// 3. API & DATA FETCHING
async function fetchSales(search = '', page = 1) {
    const loader = document.getElementById('loading-overlay');
    if (loader) loader.classList.remove('hidden');

    try {
        const response = await fetch(`/api/sales?search=${search}&page=${page}`);
        const result = await response.json();
       
        // result.data is the pagination object
        // result.data.data is the actual array of sale records
        if (result.success && result.data && Array.isArray(result.data.data)) {
            renderTable(result.data.data); // Pass the array of rows
            renderPagination(result.data);  // Pass the pagination meta-data
        } else {
            renderTable([]); 
        }
    } catch (error) {
        console.error("Fetch Error:", error);
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
    const searchInput = document.getElementById('saleSearchInput'); // Ensure ID matches your HTML
    const search = searchInput ? searchInput.value : '';
    
    // Call the fetch function we defined earlier
    fetchSales(search, page);
};
// 4. UI RENDERING LOGIC
function renderTable(sales) {
    const tableBody = document.getElementById('sales-table-body');
    if (!tableBody) return;
    
    if (!sales || sales.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10">No records found.</td></tr>';
        return;
    }
    tableBody.innerHTML = sales.map((sale, index) => generateTableRowHtml(sale, index)).join('');
}

// 5. COMPONENT TEMPLATES
function generateTableRowHtml(sale, index) {
    const status = sale.status.toLowerCase();

    // Logic: Status Color Mapping
    const statusClasses = {
        'pending': 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'paid': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
        'cancelled': 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
        'confirmed': 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
    };

    const currentStatusClass = statusClasses[status] || 'bg-neutral-100 text-neutral-600';
    const formattedTotal = parseFloat(sale.balance_subtotal || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });

    // --- Action Button Logic ---
    let actionButtons = '';

    if (status === 'paid') {
        // RULE: Only View button if Paid
        actionButtons = `
            <button onclick="viewInvoice(${sale.id})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="View & Print">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>`;
    } else if (status === 'cancelled') {
        actionButtons = `<button onclick="viewInvoice(${sale.id})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="View & Print">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>`;
    } else {
        // RULE: Hide View if Pending, but show "Check" (Checkout) button
        const viewBtn = status !== 'pending' ? `
            <button onclick="viewInvoice(${sale.id})" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg>
            </button>` : '';

        // RULE: Checkout Button (Check Icon)
        const checkBtn = `
            <button onclick="prepareCheckout(${sale.id})" class="p-2 text-emerald-600 hover:bg-indigo-100 rounded-lg" title="Finalize Payment">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </button>`;

        const editBtn = `
            <button onclick="editSale(${sale.id})" class="p-2 text-amber-600 hover:bg-indigo-100 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
            </button>`;

        const deleteBtn = status !== 'cancelled' ? `
            <button onclick="cancelSale(${sale.id})" class="p-2 text-rose-600 hover:bg-rose-100 rounded-lg" title="Cancel Booking">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>` : '';

        actionButtons = `${checkBtn} ${viewBtn} ${editBtn} ${deleteBtn}`;
    }

    return `
        <tr class="group hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 transition-all duration-200 border-b border-neutral-100 dark:border-white/5">
            <td class="px-6 py-4 text-neutral-500 font-mono text-sm">${index + 1}</td>
            <td class="px-6 py-4 font-bold text-indigo-600">${sale.invoice_no}</td>
            <td class="px-6 py-4">
                <div class="font-semibold text-neutral-900 dark:text-white">${sale.cus_first_name} ${sale.cus_last_name}</div>
                <div class="text-xs text-neutral-500">${sale.cus_contact || ''}</div>
            </td>
            <td class="px-6 py-4 text-sm">${sale.check_in_date}</td>
            <td class="px-6 py-4 text-sm">${sale.check_out_date}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase ${currentStatusClass}">
                    ${sale.status}
                </span>
            </td>
            <td class="px-6 py-4 font-bold">$${formattedTotal}</td>
            <td class="px-6 py-4 text-xs text-neutral-500">${sale.note || '-'}</td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-1">
                    ${actionButtons}
                </div>
            </td>
        </tr>
    `;
}

// 6. FORM SUBMISSION HANDLER
async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const saleId = document.getElementById('sale_id').value;

    // 1. If the button says "Confirm Payment", we ONLY update status.
    if (submitBtn.innerText.includes('Confirm')) {
        const balance = form.querySelector('input[name="balance_completion"]').value;
        return executeQuickStatusUpdate(saleId, balance);
    }

    // 2. Otherwise, we are doing a Full Edit or a New Sale.
    return executeFullSave(form, saleId);
}

// THE NUTCRACKER: Small, safe, fast.
async function executeQuickStatusUpdate(id, balance) {
    try {
        const response = await fetch(`/api/sales/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                action_type: 'checkout', // This MUST match the PHP check
                status: 'Paid', 
                balance_completion: balance 
            })
        });
        const result = await response.json();
        if (result.success) {
            Toast.fire({ icon: 'success', title: 'Paid Successfully' });
            toggleModal();
            fetchSales(); // Refresh the list to see the 'Paid' status
        }
    } catch (e) { console.error(e); }
}

// THE SLEDGEHAMMER: Full update only when editing details.
async function executeFullSave(form, saleId) {
    const formData = new FormData(form);
    
    // If saleId is null, empty string, or "undefined", it's a NEW sale
    const isNew = (!saleId || saleId === "" || saleId === "undefined");
    const url = isNew ? '/api/sales' : `/api/sales/${saleId}`;
    
    // Laravel requires _method PUT for spoofing if using FormData
    if (!isNew) {
        formData.append('_method', 'PUT');
    }

    try {
        const response = await fetch(url, { 
            method: 'POST', // Always POST, Laravel handles PUT via _method
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        if (result.success) {
            Toast.fire({ icon: 'success', title: isNew ? 'Created Successfully' : 'Updated Successfully' });
            toggleModal();
            fetchSales();
        } else {
            console.error("Server Error:", result.errors);
            Toast.fire({ icon: 'error', title: 'Save Failed' });
        }
    } catch (e) { 
        console.error("Network Error:", e); 
    }
}

// 6.1 Fetch Room Data and populate the Master Row Select
async function fetchRoomsForSelect() {
    try {
        const response = await fetch('/api/roomsmgt');
        const result = await response.json();
        
        // Handle Laravel common nested data structures
        const rooms = Array.isArray(result) ? result : (result.data || result.rooms || []);
        
        // Target the master row room select specifically using the class we added in the UI
        const masterSelect = document.querySelector('.room-item-row .room-select');
        if (!masterSelect) return;

        masterSelect.innerHTML = '<option value="">ជ្រើសរើសបន្ទប់</option>';
        
        if (rooms.length === 0) {
            console.warn("No rooms found in API response");
            return;
        }

        rooms.forEach(room => {
            const opt = document.createElement('option');
            opt.value = room.id;
            // Display Room Number and Type for clarity
            opt.text = `${room.room_number || 'N/A'} - ${room.room_type || ''}`;
            
            // Critical: Data attributes used by the calculation engine
            opt.dataset.price = room.default_unit_price || 0;
            opt.dataset.name = room.room_type || '';
            opt.dataset.status = room.status || 'Available';
            
            masterSelect.appendChild(opt);
        });

        console.log("Room list populated in master row.");
    } catch (error) {
        console.error("Error fetching rooms:", error);
        if (typeof Toast !== 'undefined') {
            Toast.fire({ icon: 'error', title: 'Could not load room list' });
        }
    }
}

// 6.2 Javascript Clone Function
// 1. Counter for unique naming
let roomIndex = 1; 
function addRoomRow() {
    const container = document.getElementById('room-items-container');
    const masterRow = container.querySelector('.room-item-row'); 
    
    const roomSelect = masterRow.querySelector('.room-select');
    // Capture the current selection BEFORE cloning
    const selectedValue = roomSelect.value; 
if (!selectedValue) {
    Swal.fire({
        title: 'សូមជ្រើសរើសបន្ទប់',
        text: 'សូមមេត្តាជ្រើសរើសបន្ទប់ជាមុនសិន មុនពេលធ្វើការបន្ថែម។',
        icon: 'warning',
        confirmButtonText: 'Understood',
        confirmButtonColor: '#4f46e5', // Matches your Indigo-600
        background: document.documentElement.classList.contains('dark') ? '#171717' : '#ffffff',
        color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1f2937',
        customClass: {
            popup: 'rounded-2xl border border-neutral-200 dark:border-white/5 shadow-2xl',
            confirmButton: 'px-6 py-2.5 rounded-xl font-bold transition-all active:scale-95'
        }
    });
    return;
}

    // 2. Clone the filled master row
    const historyRow = masterRow.cloneNode(true);
    
    // --- CRITICAL FIX START ---
    // Manually sync the selection to the clone because cloneNode(true) 
    // often fails to copy the current .value state of a select.
    const clonedSelect = historyRow.querySelector('.room-select');
    clonedSelect.value = selectedValue;
    // --- CRITICAL FIX END ---

    // 3. Transform the Clone into a "History" row
    const actionBtn = historyRow.querySelector('.add-room-row');
    actionBtn.classList.remove('bg-emerald-500', 'hover:bg-emerald-600', 'add-room-row');
    actionBtn.classList.add('bg-rose-500', 'hover:bg-rose-600', 'remove-room-row');
    actionBtn.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
        </svg>
    `;

    // Update names of the history row to the next index
    const inputs = historyRow.querySelectorAll('select, input');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            const newName = name.replace(/rooms\[\d+\]/, `rooms[${roomIndex}]`);
            input.setAttribute('name', newName);
        }
    });

    // 4. Append the filled row below the master
    container.appendChild(historyRow);
    roomIndex++;

    // 5. RESET THE MASTER ROW (Top Row) to be blank
    roomSelect.value = "";
    masterRow.querySelector('input[name*="[room_type_display]"]').value = "-";
    masterRow.querySelector('input[name*="[unit_price]"]').value = "0.00";
    masterRow.querySelector('input[name*="[food_price]"]').value = "0";
    masterRow.querySelector('input[name*="[discount]"]').value = "0";
    masterRow.querySelector('input[name*="[total_price]"]').value = "0.00";

    // Re-calculate totals
    calculateInvoice();
}
// Event Delegation for clicking the Minus button
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-room-row')) {
        const row = e.target.closest('.room-item-row');
        row.remove();
        calculateInvoice();
    }
    
    if (e.target.closest('.add-room-row')) {
        addRoomRow();
    }
});

// 6.3 Auto-fill Room Details on Selection
document.getElementById('room-items-container').addEventListener('change', function(e) {
    const roomContainer = document.getElementById('room-items-container');
    const addForm = document.getElementById('addSaleForm');

    if (roomContainer) {
        // Handle changes in the Room Rows (Food, Discount, Unit Price)
        roomContainer.addEventListener('input', function(e) {
            // We check if the user is typing in Food Price or Discount
            if (e.target.name.includes('[food_price]') || 
                e.target.name.includes('[discount]') || 
                e.target.name.includes('[unit_price]')) {
                
                console.log("Row input detected:", e.target.name); // Debugging
                calculateInvoice(); 
            }
        });
    }

    if (addForm) {
        // Handle changes in the Global Section (Booking, Completion, Dates)
        addForm.addEventListener('input', function(e) {
            const globalFields = ['booking_price', 'balance_completion', 'check_in_date', 'check_out_date'];
            if (globalFields.includes(e.target.name)) {
                calculateInvoice();
            }
        });
    }
    // Check if the changed element is the room select dropdown
    if (e.target.classList.contains('room-select')) {
        const row = e.target.closest('.room-item-row');
        const selectedOption = e.target.options[e.target.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            // Get data from the dataset attributes we created in fetchRoomsForSelect
            const price = selectedOption.dataset.price || 0;
            const type = selectedOption.dataset.name || '-';

            // Fill the readonly inputs in this specific row
            row.querySelector('input[name*="[unit_price]"]').value = parseFloat(price).toFixed(2);
            row.querySelector('input[name*="[room_type_display]"]').value = type;
        } else {
            // Reset if "Please Select" is chosen
            row.querySelector('input[name*="[unit_price]"]').value = "0.00";
            row.querySelector('input[name*="[room_type_display]"]').value = "-";
        }
        
        // Always recalculate after a selection change
        calculateInvoice();
    }

    // 2. Trigger calculation when numeric inputs change (Food, Discount)
    if (e.target.type === 'number' || e.target.type === 'text') {
        if (e.target.name.includes('food_price') || e.target.name.includes('discount')) {
            calculateInvoice();
        }
    }
});

// 6.4. Listen for changes in the Global Payment section (Booking Price, Completion)
document.getElementById('addSaleForm').addEventListener('input', function(e) {
    if (e.target.name === 'booking_price' || e.target.name === 'balance_completion') {
        calculateInvoice();
    }
});

// Helper to safely get numeric values
const getVal = (selector, parent = document) => {
    const el = parent.querySelector(selector);
    return el ? (parseFloat(el.value) || 0) : 0;
};

// 6.5 Calculate Invoice
function calculateInvoice() {
    const form = document.getElementById('addSaleForm');
    if (!form) return;

    // 1. Get Nights (Qty) - default to 1 if 0 to avoid multiplying by zero
    let nights = parseFloat(document.getElementById('nights_display').value) || 0;
    if (nights <= 0) nights = 1; 

    let totalSubtotal = 0;

    // 2. Loop through every row
    const rows = form.querySelectorAll('.room-item-row');
    rows.forEach(row => {
        const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
        const foodPrice = parseFloat(row.querySelector('input[name*="[food_price]"]').value) || 0;
        const discountPercent = parseFloat(row.querySelector('input[name*="[discount]"]').value) || 0;

        // MATH: ((Nights * UnitPrice) - Discount Amount) + FoodPrice
        const baseRoomPrice = nights * unitPrice;
        const discountAmount = baseRoomPrice * (discountPercent / 100);
        const rowTotal = (baseRoomPrice - discountAmount) + foodPrice;

        // Update the "Total Price" input for THIS row
        const rowTotalInput = row.querySelector('input[name*="[total_price]"]');
        if (rowTotalInput) {
            rowTotalInput.value = rowTotal.toFixed(2);
        }

        totalSubtotal += rowTotal;
    });

    // 3. Final Calculation at the bottom
    const bookingPrice = parseFloat(form.querySelector('input[name="booking_price"]').value) || 0;
    const completion = parseFloat(form.querySelector('input[name="balance_completion"]').value) || 0;

    const remaining = totalSubtotal - bookingPrice;
    const grandTotal = remaining - completion;

    // 4. Update Bottom Display
    const updateField = (name, value) => {
        const el = form.querySelector(`input[name="${name}"]`);
        if (el) el.value = value.toFixed(2);
    };

    updateField('balance_subtotal', totalSubtotal);
    updateField('balance_remaining', remaining);
    updateField('balance_grand_total', grandTotal);
}

// 6.6 Calculates the difference between Check-In and Check-Out in days (nights).
function calculateNights() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const nightsDisplay = document.getElementById('nights_display');

    // Run the validation
    if (!validateDates()) return;
    
    if (!checkInInput || !checkOutInput || !nightsDisplay) return;

    const checkInDate = new Date(checkInInput.value);
    const checkOutDate = new Date(checkOutInput.value);

    // Validation: Ensure both dates are selected and valid
    if (!isNaN(checkInDate.getTime()) && !isNaN(checkOutDate.getTime())) {
        
        // Calculate difference in milliseconds
        const diffInMs = checkOutDate - checkInDate;
        
        // Convert to days: ms / (1000s * 60m * 60h * 24d)
        let nights = diffInMs / (1000 * 60 * 60 * 24);

        // Ensure nights isn't negative (if checkout is before checkin)
        if (nights < 0) nights = 0;

        // Update the visible Qty field
        nightsDisplay.value = Math.ceil(nights); 

        console.log(`Nights calculated: ${nightsDisplay.value}`);
        
        // CRITICAL: Now that nights changed, update the money!
        calculateInvoice();
    } else {
        nightsDisplay.value = 0;
    }
}
// 7 Update Balance Complete
// Helper to add a row specifically for existing data (no alerts)
function buildRowFromDatabase() {
    const container = document.getElementById('room-items-container');
    const masterRow = container.querySelector('.room-item-row'); 
    const historyRow = masterRow.cloneNode(true);
    
    const actionBtn = historyRow.querySelector('.add-room-row');
    actionBtn.classList.remove('bg-emerald-500', 'add-room-row');
    actionBtn.classList.add('bg-rose-500', 'remove-room-row');
    actionBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 12H4" stroke-width="2"/></svg>`;

    // IMPORTANT: Fix the naming to match Laravel Controller expectations
    const inputs = historyRow.querySelectorAll('select, input');
    inputs.forEach(input => {
        let name = input.getAttribute('name');
        if (name) {
            // Replace old index with new roomIndex
            let newName = name.replace(/rooms\[\d+\]/, `rooms[${roomIndex}]`);
            
            // Ensure the room select is named room_id to match your findOrFail($item['room_id'])
            if (input.classList.contains('room-select')) {
                newName = `rooms[${roomIndex}][room_id]`;
            }
            input.setAttribute('name', newName);
        }
        // Clear value for new rows
        if (!input.classList.contains('room-select')) input.value = '';
    });

    container.appendChild(historyRow);
    roomIndex++;
    return historyRow;
}

async function prepareCheckout(id) {
    // 1. Fetch current data to fill the modal
    fetch(`/api/sales/${id}`)
        .then(res => res.json())
        .then(result => {
            const sale = result.data;
            
            // 2. Open Modal
            toggleModal();
            
            // 3. Fill Hidden ID
            document.getElementById('sale_id').value = sale.id;
            
            // 4. Update UI labels so handleFormSubmit knows what to do
            const modal = document.getElementById('addSaleModal');
            modal.querySelector('h2').innerText = 'Finalize Payment';
            
            const submitBtn = modal.querySelector('button[type="submit"]');
            submitBtn.innerText = 'Confirm Payment'; // CRITICAL: This text triggers the checkout logic
            submitBtn.classList.replace('bg-indigo-600', 'bg-emerald-600');

            // 5. Fill the balance field
            document.querySelector('input[name="balance_completion"]').value = sale.balance_remaining;
        });
    try {
        const response = await fetch(`/api/sales/${id}`);
        const result = await response.json();
        if (!result.success) return;
        
        const sale = result.data;
        const formEl = document.getElementById('addSaleForm'); 
        const container = document.getElementById('room-items-container');
        const addButtons = formEl.querySelectorAll('.add-room-row');
        addButtons.forEach(btn => btn.style.display = 'none');

        // 2. Hide the "Remove" (-) buttons 
        const removeButtons = formEl.querySelectorAll('.remove-room-row');
        removeButtons.forEach(btn => btn.style.display = 'none');

        // 3. Optional: Hide the entire "Action" column header if you want it even cleaner
        const actionHeader = formEl.querySelector('th:last-child'); // Assuming Action is last
        if (actionHeader) actionHeader.style.opacity = '0';
        // 1. Show Modal
        document.getElementById('addSaleModal').classList.remove('hidden');
        document.getElementById('addSaleModal').classList.add('flex');

        // 2. Clear previous clones & Reset index
        container.querySelectorAll('.remove-room-row').forEach(btn => btn.closest('.room-item-row').remove());
        roomIndex = 1;

        // 3. Populate Header
        formEl.querySelector('#sale_id').value = sale.id;
        formEl.querySelector('input[name="cus_first_name"]').value = sale.cus_first_name;
        formEl.querySelector('input[name="cus_last_name"]').value = sale.cus_last_name;
        formEl.querySelector('input[name="check_in_date"]').value = sale.check_in_date;
        formEl.querySelector('input[name="check_out_date"]').value = sale.check_out_date;
        formEl.querySelector('input[name="booking_price"]').value = sale.booking_price;

        // 4. Populate Room Rows
        if (sale.items && sale.items.length > 0) {
            sale.items.forEach((item, i) => {
                let currentRow;
                if (i === 0) {
                    currentRow = container.querySelector('.room-item-row');
                } else {
                    currentRow = buildRowFromDatabase(); // Uses the silent helper
                }

                // Fill data - Note the parseFloat to fix the ".00" issue
                currentRow.querySelector('.room-select').value = item.room_mgt_id;
                currentRow.querySelector('input[name*="[room_type_display]"]').value = item.room_type_snapshot;
                currentRow.querySelector('input[name*="[unit_price]"]').value = parseFloat(item.room_unit_price_snapshot).toFixed(2);
                currentRow.querySelector('input[name*="[food_price]"]').value = parseFloat(item.food_price).toFixed(2);
                currentRow.querySelector('input[name*="[discount]"]').value = parseFloat(item.discount_percent).toFixed(2);
                currentRow.querySelector('input[name*="[total_price]"]').value = parseFloat(item.total_price).toFixed(2);
            });
        }

        // 5. Locking Logic
        const inputs = formEl.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name !== 'balance_completion' && input.id !== 'sale_id') {
                input.readOnly = true;
                if(input.tagName === 'SELECT') input.style.pointerEvents = 'none';
            }
        });

        // 6. Final UI Setup
        const completionInput = formEl.querySelector('input[name="balance_completion"]');
        completionInput.readOnly = false;
        completionInput.value = parseFloat(sale.balance_completion || 0).toFixed(2);
        completionInput.classList.add('ring-2', 'ring-emerald-500');

        calculateNights(); // Triggers global sub-total calculation
    } catch (e) { console.error(e); }
}
window.prepareCheckout = prepareCheckout;

// 9. Status Change to paid
async function handleCheckoutUpdate(saleId, form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const completionValue = form.querySelector('input[name="balance_completion"]').value;

    submitBtn.disabled = true;
    submitBtn.innerText = 'Finalizing...';

    try {
        // We use a PUT request to update the specific sale
        const response = await fetch(`/api/sales/${saleId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                balance_completion: completionValue,
                status: 'Paid' // Force the status change to Paid
            })
        });

        const result = await response.json();

        if (result.success) {
            Toast.fire({ icon: 'success', title: 'Payment Confirmed & Checkout Complete' });
            
            toggleModal(); // Close modal
            fetchSales();  // Refresh the table to show "Paid"
        } else {
            throw new Error(result.message || 'Update failed');
        }
    } catch (error) {
        console.error("Checkout Submit Error:", error);
        Swal.fire('Error', 'Failed to complete checkout. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Confirm Payment & Checkout';
    }
}

// 10. Invoice print
/**
 * Opens the Invoice Preview Modal and populates data
 */
async function viewInvoice(id) {
    try {
        const response = await fetch(`/api/sales/${id}`);
        const result = await response.json();
        const sale = result.data;

        // Save original title to restore it later
        const originalTitle = document.title;
        const customerName = `${sale.cus_first_name} ${sale.cus_last_name}`;
        
        // --- NEW: SET CUSTOM FILENAME ---
        const InvoiceDatenow = new Date().toLocaleString("en-US", {timeZone: "Asia/Phnom_Penh"});
        document.title = `${sale.invoice_no} - ${customerName} - ${InvoiceDatenow}`;

        const status = sale.status.toLowerCase();
        let stampClass = 'stamp-pending';
        if (status === 'paid') stampClass = 'stamp-paid';
        if (status === 'cancelled') stampClass = 'stamp-cancelled';

        [1, 2].forEach(num => {
            document.getElementById(`p-invoice-no-${num}`).innerText = sale.invoice_no;
            document.getElementById(`p-customer-name-${num}`).innerText = customerName;
            document.getElementById(`p-customer-contact-${num}`).innerText = sale.cus_contact || 'N/A';
            document.getElementById(`p-check-in-${num}`).innerText = sale.check_in_date;
            document.getElementById(`p-check-out-${num}`).innerText = sale.check_out_date;
            document.getElementById(`p-nights-${num}`).innerText = `${sale.qty} Nights`;
            
            document.getElementById(`p-subtotal-${num}`).innerText = `$${parseFloat(sale.balance_subtotal).toFixed(2)}`;
            document.getElementById(`p-booking-${num}`).innerText = `-$${parseFloat(sale.booking_price).toFixed(2)}`;
            document.getElementById(`p-grand-total-${num}`).innerText = `$${parseFloat(sale.balance_remaining).toFixed(2)}`;
            
            const stampEl = document.getElementById(`p-stamp-${num}`);
            stampEl.innerText = sale.status;
            stampEl.className = `status-stamp ${stampClass}`;
            document.getElementById(`p-status-text-${num}`).innerText = `Folio is currently ${sale.status.toUpperCase()}`;
            

            const rows = sale.items.map(item => {
                const roomNo = item.room_number_snapshot || 'N/A';
                return `
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="font-medium text-slate-800">${item.room_type_snapshot}-${roomNo}
                        </td>
                        <td class="p-3 text-center text-slate-900">${parseFloat(item.food_price || 0).toFixed(2)}</td>
                        <td class="p-3 text-right text-slate-900">$${parseFloat(item.room_unit_price_snapshot).toFixed(2)}</td>
                        <td class="p-3 text-right text-red-500">% ${parseFloat(item.discount_percent).toFixed(2)}</td>
                        <td class="p-3 text-right font-black text-slate-900">$${parseFloat(item.total_price).toFixed(2)}</td>
                    </tr>
                `;
            }).join('');
            document.getElementById(`p-items-list-${num}`).innerHTML = rows;
        });

        // Trigger Print
        setTimeout(() => { 
            window.print(); 
            // Restore original title after a slight delay so the UI doesn't flicker
            setTimeout(() => { document.title = originalTitle; }, 1000);
        }, 500);

    } catch (e) {
        console.error("Print Error:", e);
    }
}

function closeViewModal() {
    const modal = document.getElementById('viewInvoiceModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.printInvoice = function() {
    window.print(); // Browser will use the @media print CSS to clean up
}
// Export to Global
window.viewInvoice = viewInvoice;
window.printInvoice = printInvoice;
// Helper to clean up form locks
function resetFormState() {
    const activeForm = document.getElementById('addSaleForm');
    if (!activeForm) return;

    const allInputs = activeForm.querySelectorAll('input, select, textarea');
    
    allInputs.forEach(input => {
        input.readOnly = false;
        input.disabled = false;
        input.style.pointerEvents = 'auto'; // Re-enable select clicks
        input.classList.remove('ring-2', 'ring-emerald-500');
    });

    // Show the + Add Room button again
    const addBtn = activeForm.querySelector('.add-room-row');
    if (addBtn) addBtn.style.display = 'block';
}

window.cancelSale = cancelSale;
// 9. CANCEL LOGIC (Rule: Changes status, doesn't delete row)
async function cancelSale(id) {
    const confirm = await Swal.fire({
        title: 'Cancel Booking?',
        text: "This will change the status to Cancelled.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48', // Rose-600
        cancelButtonColor: '#6b7280',  // Gray-500
        confirmButtonText: 'Yes, cancel it'
    });

    if (confirm.isConfirmed) {
        try {
            const response = await fetch(`/api/sales/${id}`, {
                method: 'DELETE', // Still using DELETE verb to hit the destroy method
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                Toast.fire({ icon: 'success', title: result.message });
                fetchSales(); // Refresh the table to show the new "Cancelled" status
            }
        } catch (error) {
            console.error("Cancellation Error:", error);
            Toast.fire({ icon: 'error', title: 'Operation failed' });
        }
    }
}

// 10. Edit Data
async function editSale(id) {
    try {
        // 1. Fetch data from API
        const response = await fetch(`/api/sales/${id}`);
        const result = await response.json();
        if (!result.success) return;
        
        const sale = result.data;
        const modalEl = document.getElementById('addSaleModal');
        const formEl = document.getElementById('addSaleForm');
        const container = document.getElementById('room-items-container');

        // 1. Define what you want to LOCK (simple list of 'name' attributes)
const lockedFields = ['invoice_no', 'sale_id']; 

// 2. Apply the lock
const allInputs = formEl.querySelectorAll('input, select, textarea');
allInputs.forEach(input => {
    if (lockedFields.includes(input.name) || input.id === 'sale_id') {
        input.readOnly = true;
        input.classList.add('bg-slate-100', 'cursor-not-allowed'); // Visual feedback
        if(input.tagName === 'SELECT') input.style.pointerEvents = 'none';
    } else {
        // Ensure everything else is editable (fixes leftover checkout locks)
        input.readOnly = false;
        input.classList.remove('bg-slate-100', 'cursor-not-allowed');
        input.style.pointerEvents = 'auto';
    }
});

        // 2. Open Modal and Update UI for Edit Mode
        modalEl.classList.remove('hidden');
        modalEl.classList.add('flex');
        modalEl.querySelector('h2').innerText = `Edit Booking: ${sale.invoice_no}`;
        
        const submitBtn = formEl.querySelector('button[type="submit"]');
        submitBtn.innerText = 'Update Booking';
        submitBtn.classList.replace('bg-emerald-600', 'bg-amber-600');
        submitBtn.classList.replace('bg-indigo-600', 'bg-amber-600');

        // 3. Reset Modal State (Ensure buttons are visible and inputs editable)
        resetFormState(); 
        
        // 4. Fill Header Data
        formEl.querySelector('#sale_id').value = sale.id;
        formEl.querySelector('input[name="cus_first_name"]').value = sale.cus_first_name;
        formEl.querySelector('input[name="cus_last_name"]').value = sale.cus_last_name;
        formEl.querySelector('input[name="cus_contact"]').value = sale.cus_contact;
        formEl.querySelector('input[name="check_in_date"]').value = sale.check_in_date;
        formEl.querySelector('input[name="check_out_date"]').value = sale.check_out_date;
        formEl.querySelector('input[name="booking_price"]').value = sale.booking_price;
        const balComp = parseFloat(sale.balance_completion || 0).toFixed(2);
        formEl.querySelector('input[name="balance_completion"]').value = balComp;

        // 5. Clear previous clones & Populate Room Rows
        container.querySelectorAll('.remove-room-row').forEach(btn => btn.closest('.room-item-row').remove());
        roomIndex = 1;

        if (sale.items && sale.items.length > 0) {
            sale.items.forEach((item, i) => {
                let currentRow;
                if (i === 0) {
                    currentRow = container.querySelector('.room-item-row');
                } else {
                    // Use the helper we built to add rows without triggering "Please select room" alerts
                    currentRow = buildRowFromDatabase(); 
                }

                // Fill Row Data
                currentRow.querySelector('.room-select').value = item.room_mgt_id;
                currentRow.querySelector('input[name*="[room_type_display]"]').value = item.room_type_snapshot;
                currentRow.querySelector('input[name*="[unit_price]"]').value = parseFloat(item.room_unit_price_snapshot).toFixed(2);
                currentRow.querySelector('input[name*="[food_price]"]').value = parseFloat(item.food_price).toFixed(2);
                currentRow.querySelector('input[name*="[discount]"]').value = parseFloat(item.discount_percent).toFixed(2);
                currentRow.querySelector('input[name*="[total_price]"]').value = parseFloat(item.total_price).toFixed(2);
            });
        }

        // 6. Recalculate
        calculateNights();
        calculateInvoice();

    } catch (e) {
        console.error("Edit Sale Error:", e);
        Toast.fire({ icon: 'error', title: 'Failed to load booking data' });
    }
}
window.editSale = editSale;

/**
 * Validates that Check-In is before Check-Out
 * Returns true if valid, false if invalid
 */
function validateDates() {
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;

    if (!checkIn || !checkOut) return true; // Don't alert if fields are empty yet

    const start = new Date(checkIn);
    const end = new Date(checkOut);

    if (start >= end) {
        Swal.fire({
            title: 'កាលបរិច្ឆេទមិនត្រឹមត្រូវ', // Invalid Date
            text: 'ថ្ងៃចេញត្រូវតែក្រោយថ្ងៃចូល!',
            icon: 'error',
            confirmButtonColor: '#4f46e5',
        });
        
        // Reset the check-out field so they have to pick again
        document.getElementById('check_out').value = '';
        document.getElementById('nights_display').value = 0;
        return false;
    }
    return true;
}

// Optional: Add to your dashboard.js
function handleExport(element) {
    const originalText = element.innerHTML;
    element.innerHTML = 'Generating...';
    element.classList.add('opacity-50', 'pointer-events-none');
    
    // The download will happen, and after 3 seconds we reset the button
    setTimeout(() => {
        element.innerHTML = originalText;
        element.classList.remove('opacity-50', 'pointer-events-none');
    }, 3000);
}