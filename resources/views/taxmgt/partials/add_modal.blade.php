<div id="addTaxModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-neutral-900/60 backdrop-blur-md p-2 sm:p-4">
    <div class="bg-white dark:bg-neutral-900 w-full max-w-2xl rounded-2xl shadow-2xl border border-neutral-200 dark:border-white/5 overflow-hidden transform transition-all max-h-[95vh] flex flex-col">
        
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex justify-between items-center bg-white dark:bg-neutral-900 sticky top-0 z-10">
            <h2 class="text-lg sm:text-xl font-semibold text-neutral-800 dark:text-white truncate mr-2">បង្កើតវិក្កយបត្រពន្ធ (Add Tax Invoice)</h2>
            <button type="button" onclick="toggleModal()" class="flex-shrink-0 text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="addTaxForm" class="p-4 sm:p-6 space-y-5 overflow-y-auto">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Select Sale Invoice</label>
                <div class="relative">
                    <select name="sale_mgt_id" id="sale_select" onchange="updateBasePrice()" required
                        class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white appearance-none">
                        <option value="">Please select a sale</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Tax Invoice Number</label>
                    <input type="text" name="tax_invoice_number" required placeholder="VAT-2026-XXXX"
                        class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white">
                </div>

                <div class="col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Customer VAT TIN</label>
                    <input type="text" name="tax_cus_vattin" placeholder="Optional"
                        class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white">
                </div>

                <div class="col-span-1 sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Customer Address</label>
                    <input type="text" name="tax_cus_address" placeholder="Phnom Penh, Cambodia..."
                        class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white">
                </div>

                <div class="col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Original Subtotal ($)</label>
                    <input type="number" id="base_subtotal" readonly
                        class="w-full px-4 py-2.5 bg-neutral-100 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-500 cursor-not-allowed outline-none">
                </div>

                <div class="col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-indigo-500 mb-1.5 ml-1">Hidden Price ($)</label>
                    <input type="number" step="0.01" name="tax_hidden_price" id="hidden_price" value="0" oninput="calculateTaxTotals()"
                        class="w-full px-4 py-2.5 bg-indigo-50/30 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/20 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white">
                </div>

                <div class="col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">AT Price ($)</label>
                    <input type="number" step="0.01" name="tax_at_price" id="at_price" value="0" oninput="calculateTaxTotals()"
                        class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white">
                </div>

                <div class="col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-rose-500 mb-1.5 ml-1">VAT (10%)</label>
                    <input type="number" id="vat_display" readonly
                        class="w-full px-4 py-2.5 bg-rose-50/30 dark:bg-rose-500/5 border border-rose-100 dark:border-rose-500/20 rounded-xl text-rose-600 font-bold outline-none">
                </div>
            </div>

            <div class="p-4 bg-neutral-100 dark:bg-neutral-800 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-widest text-neutral-500">Grand Total</span>
                <span class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400" id="final_total_display">$0.00</span>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4">
                <button type="button" onclick="toggleModal()" 
                    class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                    class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 transform active:scale-95 transition-all">
                    Create Tax Invoice
                </button>
            </div>
        </form>
    </div>
</div>