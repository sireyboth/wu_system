// 1. GLOBAL UTILITIES & CONFIG
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

const form = document.getElementById('addTaxForm');

// 2. CORE INITIALIZATION
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('taxSearchInput');
    let debounceTimer;

    // Initial Load
    fetchTaxs();

    // Event Listeners
   if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        // FIX: Change fetchRooms to fetchTaxs
        debounceTimer = setTimeout(() => fetchTaxs(e.target.value), 300);
    });
}
});

// 3. API & DATA FETCHING
async function fetchTaxs(search = '') {
    const loader = document.getElementById('loading-overlay');
    if (loader) loader.classList.remove('hidden');

    try {
        // 1. Ensure the URL matches your api.php (Route::apiResource('tax', ...))
        const response = await fetch(`/api/taxmgt?search=${search}`);
        const result = await response.json();

        // 2. LOGIC FIX:
        // Since your API uses ->get(), 'result.data' IS the array.
        // We do NOT need '.data.data' here.
        if (result.success && Array.isArray(result.data)) {
            renderTable(result.data);
        } else {
            console.warn("Data is not an array or success is false", result);
            renderTable([]);
        }
    } catch (error) {
        console.error("Fetch Error:", error);
        Toast.fire({ icon: 'error', title: 'Failed to load data' });
        renderTable([]);
    } finally {
        if (loader) loader.classList.add('hidden');
    }
}

// 4. UI RENDERING LOGIC
function renderTable(taxs) {
    const tableBody = document.getElementById('tax-table-body');
    if (!tableBody) return;

    if (!taxs || taxs.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-10">No records found.</td></tr>';
        return;
    }
    tableBody.innerHTML = taxs.map((tax, index) => generateTableRowHtml(tax, index)).join('');
}

// 5. COMPONENT TEMPLATES
function generateTableRowHtml(tax, index) {
    // Logic: Status Color Mapping
    const statusClasses = {
        'pending': 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'paid': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
        'cancelled': 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
        'confirmed': 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
    };

    // Define the missing variables
    const formattedTotal = parseFloat(tax.tax_balance_final || 0).toFixed(2);
    const currentStatusClass = statusClasses[tax.sale.status] || 'bg-neutral-100 text-neutral-600';

    // Accessing nested sale data carefully
    const customerName = tax.sale
        ? `${tax.sale.cus_first_name} ${tax.sale.cus_last_name}`
        : 'N/A';

    return `
        <tr class="group hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 transition-all border-b dark:border-white/5">
            <td class="px-6 py-4 font-mono text-sm">${index + 1}</td>
            <td class="px-6 py-4 font-bold text-indigo-600">${tax.tax_invoice_number}</td>
            <td class="px-6 py-4">
                <div class="font-semibold text-neutral-900 dark:text-white">${customerName}</div>
            </td>
            <td class="px-6 py-4 text-sm">$${parseFloat(tax.tax_sub_total).toFixed(2)}</td>
            <td class="px-6 py-4 text-sm">$${parseFloat(tax.tax_vat_price).toFixed(2)}</td>
            <td class="px-6 py-4 text-sm">$${parseFloat(tax.tax_at_price).toFixed(2)}</td>
            <td class="px-6 py-4 font-bold text-neutral-900 dark:text-white">$${formattedTotal}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase ${currentStatusClass}">
                    ${tax.sale.status}
                </span>
            </td>
            <td class="px-6 py-4 text-right">
                <button onclick="viewInvoiceTax(${tax.id})" class="p-2 text-blue hover:bg-white rounded-lg" title="View Tax">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <button onclick="editTax(${tax.id})" class="p-2 text-amber-600 hover:bg-indigo-100 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
            </button>
                <button onclick="cancelTax(${tax.id})" class="p-2 text-rose-600 hover:bg-white rounded-lg" title="Cancel Booking">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </td>
        </tr>
    `;
}

// Global storage
let salesList = [];
// 1. Fetch Sales for Dropdown
async function fetchSalesDropdown() {
  try {
        const response = await fetch('/api/sales');
        const result = await response.json();

        if (result.success) {
            salesList = result.data.data ? result.data.data : result.data;
            const select = document.getElementById('sale_select');
            select.innerHTML = '<option value="">Please select a sale</option>' +
                salesList.map(sale => `
                    <option value="${sale.id}">${sale.invoice_no} - ${sale.cus_first_name}</option>
                `).join('');
            return true; // Success signal
        }
    } catch (error) {
        console.error("Fetch error", error);
        return false;
    }
}

// 2. Math Logic
function calculateTaxTotals() {
    const base = parseFloat(document.getElementById('base_subtotal').value || 0);
    const hidden = parseFloat(document.getElementById('hidden_price').value || 0);
    const at = parseFloat(document.getElementById('at_price').value || 0);

    const newSubTotal = base + hidden + at;
    const vat = newSubTotal * 0.10;
    const grandTotal = newSubTotal + vat;

    document.getElementById('vat_display').value = vat.toFixed(2);
    document.getElementById('final_total_display').innerText = `$${grandTotal.toFixed(2)}`;
}

// 3. Global Window Exports (Crucial for Vite)
window.updateBasePrice = function() {
    const saleId = document.getElementById('sale_select').value;
    const selectedSale = salesList.find(s => s.id == saleId);
    if (selectedSale) {
        document.getElementById('base_subtotal').value = selectedSale.balance_subtotal;
        calculateTaxTotals();
    }
};

window.calculateTaxTotals = calculateTaxTotals;
window.toggleModal = function() {
    const modal = document.getElementById('addTaxModal');
    const form = document.getElementById('addTaxForm');

    if (modal.classList.contains('hidden')) {
        // Opening
        modal.classList.replace('hidden', 'flex');
        fetchSalesDropdown();
    } else {
        // Closing
        modal.classList.replace('flex', 'hidden');
        form.reset();
        form.dataset.mode = 'add'; // Reset to Add mode
        document.querySelector('#addTaxModal h2').innerText = 'បង្កើតវិក្កយបត្រពន្ធ (Add Tax Invoice)';
    }
};

// 4. Form Submission Logic
form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const mode = this.dataset.mode || 'add';
    const id = this.dataset.id;
    const url = mode === 'edit' ? `/api/taxmgt/${id}` : '/api/taxmgt';
    const method = mode === 'edit' ? 'PUT' : 'POST';

    const formData = new FormData(this);

    // Convert FormData to JSON for PUT requests (Standard for Laravel APIs)
    let bodyData = formData;
    let headers = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
    };

    if (mode === 'edit') {
        const json = Object.fromEntries(formData.entries());
        bodyData = JSON.stringify(json);
        headers['Content-Type'] = 'application/json';
    }

    try {
        const response = await fetch(url, { method, body: bodyData, headers });
        const result = await response.json();

        if (result.success) {
            Toast.fire({ icon: 'success', title: 'រក្សាទុកបានជោគជ័យ' });
            window.toggleModal();
            fetchTaxs();
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        console.error("Error:", error);
    }
});

// 1. Edit Function: Fetch data and fill modal
window.editTax = async function(id) {
   try {
        // 1. Start fetching the specific tax record
        const response = await fetch(`/api/taxmgt/${id}`);
        const result = await response.json();

        if (result.success) {
            const tax = result.data;

            // 2. Open the modal container first (but don't trigger internal fetch yet)
            const modal = document.getElementById('addTaxModal');
            modal.classList.replace('hidden', 'flex');

            // 3. IMPORTANT: Wait for the dropdown to finish loading options
            await fetchSalesDropdown();

            // 4. Now that options exist, we can select the right one
            document.getElementById('sale_select').value = tax.sale_mgt_id;


            // Fill rest of fields
            document.querySelector('input[name="tax_invoice_number"]').value = tax.tax_invoice_number;
            document.querySelector('input[name="tax_cus_vattin"]').value = tax.tax_cus_vattin || '';
            document.querySelector('input[name="tax_cus_address"]').value = tax.tax_cus_address || '';
            document.getElementById('base_subtotal').value = tax.tax_sub_total;
            document.getElementById('hidden_price').value = tax.tax_hidden_price;
            document.getElementById('at_price').value = tax.tax_at_price;

            // FIX: Force these to be Numbers using Number() or parseFloat()
            document.getElementById('base_subtotal').value = Number(tax.tax_sub_total).toFixed(2);
            document.getElementById('hidden_price').value = Number(tax.tax_hidden_price).toFixed(2);
            document.getElementById('at_price').value = Number(tax.tax_at_price).toFixed(2);
            calculateTaxTotals();

            // Set mode
            const form = document.getElementById('addTaxForm');
            form.dataset.mode = 'edit';
            form.dataset.id = id;
            document.querySelector('#addTaxModal h2').innerText = 'កែសម្រួលវិក្កយបត្រពន្ធ (Edit Tax Invoice)';
        }
    } catch (error) {
        console.error("Edit Error:", error);
    }
};

window.viewInvoiceTax = async function(id) {
    try {
        // 1. Fetch Tax Record (Ensure your API returns the 'sale' and 'sale.items' relationship)
        const response = await fetch(`/api/taxmgt/${id}`);
        const result = await response.json();
        const tax = result.data;
        const sale = tax.sale; // Accessed via relationship

        const originalTitle = document.title;
        document.title = `TAX_${tax.tax_invoice_number}_${sale.cus_first_name}`;

        [1, 2].forEach(num => {
            // Header & Customer Info
            document.getElementById(`p-tax-no-${num}`).innerText = tax.tax_invoice_number;
            document.getElementById(`p-tax-customer-${num}`).innerText = `${sale.cus_first_name} ${sale.cus_last_name}`;
            document.getElementById(`p-tax-address-${num}`).innerText = tax.tax_cus_address || 'N/A';
            document.getElementById(`p-tax-vattin-${num}`).innerText = tax.tax_cus_vattin || 'N/A';

            // Items Loop (from SaleItems)
            const itemRows = sale.items.map(item => `
                <tr>
                    <td class="p-2">${item.room_type_snapshot} - ${item.room_number_snapshot}</td>
                    <td class="p-2 text-left">${sale.qty}</td>
                    <td class="p-2 text-left">$${Number(item.room_unit_price_snapshot).toFixed(2)}</td>
                    <td class="p-2 text-left">$${Number(item.total_price).toFixed(2)}</td>
                </tr>
            `).join('');
            document.getElementById(`p-tax-items-${num}`).innerHTML = itemRows;

            // Math Totals
            const extraPrice = Number(tax.tax_hidden_price) + Number(tax.tax_at_price);
            const totalBeforeTax = Number(tax.tax_sub_total) + extraPrice;

            document.getElementById(`p-tax-base-${num}`).innerText = `$${Number(tax.tax_sub_total).toFixed(2)}`;
            document.getElementById(`p-tax-extra-${num}`).innerText = `+$${extraPrice.toFixed(2)}`;
            // document.getElementById(`p-tax-total-before-${num}`).innerText = `$${totalBeforeTax.toFixed(2)}`;
            document.getElementById(`p-tax-vat-${num}`).innerText = `$${Number(tax.tax_vat_price).toFixed(2)}`;
            document.getElementById(`p-tax-grand-total-${num}`).innerText = `$${Number(tax.tax_balance_final).toFixed(2)}`;
        });

        // Trigger Print
        setTimeout(() => {
            window.print();
            document.title = originalTitle;
        }, 500);

    } catch (e) {
        console.error("Print Error:", e);
        Swal.fire('Error', 'Could not generate tax invoice', 'error');
    }
}

// 2. Cancel/Delete Function: Confirmation and API Call
window.cancelTax = function(id) {
    Swal.fire({
        title: 'តើអ្នកប្រាកដទេ?',
        text: "អ្នកនឹងមិនអាចត្រឡប់វាវិញបានទេ!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'បាទ/ចាស លុបវា!',
        cancelButtonText: 'បោះបង់'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/api/taxmgt/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire('ជោគជ័យ!', 'វិក្កយបត្រត្រូវបានលុប។', 'success');
                    fetchTaxs(); // Refresh table
                }
            } catch (error) {
                Swal.fire('កំហុស!', 'មិនអាចលុបទិន្នន័យបានទេ។', 'error');
            }
        }
    });
};

