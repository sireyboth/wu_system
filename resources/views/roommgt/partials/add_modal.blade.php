        <div id="addRoomModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-neutral-900/60 backdrop-blur-md p-4">

            <div class="bg-white dark:bg-neutral-900 w-full max-w-md rounded-2xl shadow-2xl border border-neutral-200 dark:border-white/5 overflow-hidden transform transition-all">
                
                <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-neutral-800 dark:text-white">Add New Room</h2>
                    <button type="button" onclick="toggleModal()" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form id="addRoomForm" class="p-6 space-y-5">
                    @csrf
                    
                    <input type="hidden" name="id" id="room_id">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Room Number</label>
                            <input type="text" name="room_number" placeholder="e.g. 301" required
                                class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all dark:text-white">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Unit Price ($)</label>
                            <input type="number" step="0.01" name="default_unit_price" placeholder="0.00" required
                                class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Room Type</label>
                        <select name="room_type" required
                            class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white appearance-none">
                            <option value="">Please select room</option>
                            <option value="Sea View Double">Sea View Double Room</option>
                            <option value="Mountain View Twin">Mountain View Twin Room</option>
                            <option value="Sea View Deluxe">Sea View Deluxe Room</option>
                            <option value="Sea View Twin">Sea View Twin Room</option>
                            <option value="Family">Family Room</option>
                        </select>
                    </div>

                    <div>
                    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2 ml-1">Room Status</label>
        <div class="grid grid-cols-3 gap-2 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl">
            <label class="cursor-pointer">
                <input type="radio" name="status" value="available" class="peer sr-only" checked>
                <div class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-all
                    peer-checked:bg-white dark:peer-checked:bg-neutral-700 peer-checked:text-emerald-600 peer-checked:shadow-sm
                    text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Available
                </div>
            </label>

            <label class="cursor-pointer">
                <input type="radio" name="status" value="occupied" class="peer sr-only">
                <div class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-all
                    peer-checked:bg-white dark:peer-checked:bg-neutral-700 peer-checked:text-amber-600 peer-checked:shadow-sm
                    text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    occupied
                </div>
            </label>

            <label class="cursor-pointer">
                <input type="radio" name="status" value="maintenance" class="peer sr-only">
                <div class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-all
                    peer-checked:bg-white dark:peer-checked:bg-neutral-700 peer-checked:text-rose-600 peer-checked:shadow-sm
                    text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Maintenace
                </div>
            </label>
        </div>
    </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5 ml-1">Internal Note</label>
                        <textarea name="note" rows="2" placeholder="Add any specific details..."
                            class="w-full px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:text-white resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" onclick="toggleModal()" 
                            class="px-5 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 transform active:scale-95 transition-all">
                            Create Room
                        </button>
                    </div>
                </form>
            </div>
        </div>