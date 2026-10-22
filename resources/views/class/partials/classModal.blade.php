<div id="classModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <div id="classModalCard"
         class="w-full max-w-3xl max-h-[90vh] flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden">

        <div class="shrink-0 px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900">
            <h3 id="classModalTitle" class="text-lg font-bold text-neutral-900 dark:text-white">Create Class</h3>
            <button type="button" onclick="ClassModal.toggle(false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="classForm" class="flex-1 min-h-0 overflow-y-auto p-6 space-y-5 bg-white dark:bg-neutral-900">
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Subject</label>
                <input required type="text" name="subject_search" id="classSubjectSearch" list="subjectsDatalist"
                    placeholder="Type to search by code or name..."
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                <datalist id="subjectsDatalist"></datalist>
                <input type="hidden" name="subject_id" id="classSubjectId">
                <p class="text-xs text-rose-500 mt-1 hidden" id="classSubjectHint">Pick a subject from the list — typing a code/name alone doesn't select it.</p>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Term</label>
                    <select required name="term_id" id="classTermSelect"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="" disabled selected>-- select term --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Code</label>
                    <input required type="text" name="code" placeholder="e.g., IT101-A"
                        class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Campus</label>
                    <select required name="campus_id" id="classCampusSelect"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="" disabled selected>-- select campus --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Shift</label>
                    <select required name="shift_id" id="classShiftSelect"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="" disabled selected>-- select shift --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Capacity</label>
                    <input type="number" name="capacity" min="1" placeholder="Optional"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Major <span class="normal-case font-normal text-neutral-400">(pick any number — label only)</span></label>
                    <div id="classMajorChecklist" class="max-h-40 overflow-y-auto grid grid-cols-2 gap-x-3 gap-y-1 px-3 py-2.5 bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl"></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Faculty</label>
                    <input type="text" id="classFacultyDisplay" disabled placeholder="Auto-filled from Major"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-100 dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-500 dark:text-neutral-400 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Batch</label>
                    <select required name="batch_id" id="classBatchSelect"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="" disabled selected>-- select batch --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Room Number</label>
                    <input type="text" name="room_number" placeholder="e.g., A-204"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Time</label>
                    <select name="time_slot" id="classTimeSlotSelect"
                        class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="">-- any --</option>
                        <option value="8:00-11:10">8:00-11:10</option>
                        <option value="2:00-5:00">2:00-5:00</option>
                        <option value="5:30-8:30">5:30-8:30</option>
                    </select>
                </div>
            </div>

            <div id="classLecturerField">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">Lecturer <span class="normal-case font-normal text-neutral-400">(optional — assigned as Primary)</span></label>
                <input type="text" name="lecturer_search" id="classLecturerSearch" list="lecturersDatalist" autocomplete="off"
                    placeholder="Type to search by code or name..."
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                <datalist id="lecturersDatalist"></datalist>
                <input type="hidden" name="lecturer_id" id="classLecturerId">
                <p class="text-xs text-rose-500 mt-1 hidden" id="classLecturerHint">Pick a lecturer from the list — typing a name alone doesn't select it.</p>
            </div>
            <p class="text-xs text-neutral-400 hidden" id="classLecturerEditHint">Use the <b>Lecturer</b> button on the class row to reassign — editing here doesn't change it.</p>
        </form>

        <div class="shrink-0 flex justify-end items-center gap-3 px-6 py-4 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900">
            <button type="button" onclick="ClassModal.toggle(false)"
                class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
                Cancel
            </button>
            <button type="submit" form="classForm" id="classSubmitBtn"
                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-indigo-500/20 active:scale-95 rounded-xl transition-all duration-200">
                Save
            </button>
        </div>
    </div>
</div>
