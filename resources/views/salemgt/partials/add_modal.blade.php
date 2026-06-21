<div id="addSaleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-neutral-900/60 backdrop-blur-md p-2 sm:p-4">
    <div class="bg-white dark:bg-neutral-900 w-full max-w-6xl rounded-2xl shadow-2xl border border-neutral-200 dark:border-white/5 overflow-hidden transform transition-all">
        
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-neutral-800 dark:text-white">Create New Booking</h2>
            <button type="button" onclick="toggleModal()" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="addSaleForm" class="p-4 sm:p-6 overflow-y-auto max-h-[calc(100vh-100px)]">
            @csrf
            <input type="hidden" name="id" id="sale_id">

            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"></path></svg>
                    </span>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-neutral-400">Customer & Stay Info</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1 ml-1">First Name</label>
                        <input type="text" name="cus_first_name" placeholder="គោត្តនាម" required class="w-full px-3 py-2 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-600 outline-none dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1 ml-1">Last Name</label>
                        <input type="text" name="cus_last_name" placeholder="នាម" required class="w-full px-3 py-2 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-600 outline-none dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1 ml-1">Contact</label>
                        <input type="text" name="cus_contact" placeholder="ទំនាក់ទំនង" required class="w-full px-3 py-2 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-600 outline-none dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1 ml-1 text-indigo-500">Check-In</label>
                        <input type="date" name="check_in_date" id="check_in" required class="w-full px-3 py-2 bg-indigo-50/50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl focus:ring-2 focus:ring-indigo-600 outline-none dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1 ml-1 text-indigo-500">Check-Out</label>
                        <input type="date" name="check_out_date" id="check_out" required class="w-full px-3 py-2 bg-indigo-50/50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl focus:ring-2 focus:ring-indigo-600 outline-none dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1 ml-1">Qty (Nights)</label>
                        <input type="text" name="qty" id="nights_display" readonly class="w-full px-3 py-2 bg-neutral-100 dark:bg-neutral-700 border-none rounded-xl text-neutral-500 font-bold" value="0">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="bg-amber-100 text-amber-600 p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"></path></svg>
                        </span>
                        <h3 class="text-sm font-bold uppercase tracking-widest text-neutral-400">Room Selection</h3>
                    </div>
                </div>

                <div id="room-items-container" class="space-y-3">
                    <div class="room-item-row grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end p-3 bg-neutral-50 dark:bg-neutral-800/30 rounded-2xl border border-neutral-100 dark:border-white/5">
                        <div class="col-span-1 lg:col-span-2">
                            <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1 ml-1">Select Room</label>
                            <select name="rooms[0][room_id]" required class="room-select w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500 dark:text-white text-sm">
                                <option value="a">ជ្រើសរើសបន្ទប់</option>
                            </select>
                        </div>
                        <div class="col-span-1 lg:col-span-2">
                            <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1 ml-1">Room Type</label>
                            <input type="text" readonly name="rooms[0][room_type_display]" class="w-full px-3 py-2 bg-neutral-100 dark:bg-neutral-700 border-none rounded-xl text-neutral-400 text-xs" value="-">
                        </div>
                        <div class="col-span-1 lg:col-span-1">
                            <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1 ml-1">Unit Price</label>
                            <input type="text" readonly name="rooms[0][unit_price]" class="w-full px-3 py-2 bg-neutral-100 dark:bg-neutral-700 border-none rounded-xl text-neutral-400 text-sm" value="0.00">
                        </div>
                        <div class="col-span-1 lg:col-span-2">
                            <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1 ml-1">Food Price</label>
                            <input type="number" step="0.01" name="rooms[0][food_price]" class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none dark:text-white text-sm" value="0">
                        </div>
                        <div class="col-span-1 lg:col-span-2">
                            <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1 ml-1">Discount (%)</label>
                            <input type="number" name="rooms[0][discount]" class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none dark:text-white text-sm" value="0">
                        </div>
                        <div class="col-span-1 lg:col-span-2">
                            <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1 ml-1">Total Price</label>
                            <input type="text" readonly name="rooms[0][total_price]" class="w-full px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border-none rounded-xl text-amber-600 font-bold text-sm" value="0.00">
                        </div>
                        <div class="col-span-1 lg:col-span-1 flex justify-center pb-1">
                            <button type="button" class="add-room-row w-full lg:w-auto p-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-all shadow-lg shadow-emerald-500/20">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="bg-emerald-100 text-emerald-600 p-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"></path></svg>
                    </span>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-neutral-400">Payment Summary</h3>
                </div>
                
                <div class="p-4 sm:p-6 bg-neutral-50 dark:bg-black rounded-2xl border dark:border-neutral-800 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1.5">Booking Price ($)</label>
                            <input type="number" step="0.01" name="booking_price" class="w-full px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-800 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1.5">Sub Total</label>
                            <input type="text" name="balance_subtotal" readonly class="w-full px-4 py-2 bg-neutral-100 dark:bg-neutral-800/50 border-none rounded-xl text-neutral-400">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1.5">Remain</label>
                            <input type="text" name="balance_remaining" readonly class="w-full px-4 py-2 bg-neutral-100 dark:bg-neutral-800/50 border-none rounded-xl text-amber-500 ">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1.5 ">Complete (Manual)</label>
                            <input type="number" placeholder="0.00" readonly step="0.01" name="balance_completion" class="w-full px-4 py-2 bg-neutral-100 dark:bg-neutral-800/50 border-none rounded-xl text-amber-500  focus:ring-emerald-500 outline-none">
                        </div>
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block text-[10px] font-bold uppercase text-neutral-500 mb-1.5">Grand Total</label>
                            <input type="text" name="balance_grand_total" readonly class="w-full px-4 py-2 bg-neutral-100 border-none  dark:bg-neutral-800/50 rounded-xl text-emerald-400 font-bold ">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-white/5">
                <button type="button" onclick="toggleModal()" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300">Cancel</button>
                <button type="submit" class="w-full sm:w-auto px-10 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    Save Booking
                </button>
            </div>
        </form>
    </div>
</div>