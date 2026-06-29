<!-- resources/views/lecturer.blade.php (or your lecturer blade layout) -->

<!-- 1. Main Modal Backdrop Container -->
<div id="lecturerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-neutral-950/40 backdrop-blur-sm p-4 animate-fade-in">

    <!-- 2. Modal Card Wrapper Box -->
    <div class="relative w-full max-w-lg bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-white/5 rounded-2xl shadow-2xl transition-all transform scale-100 p-6">

        <!-- Header Section -->
        <div class="flex items-center justify-between border-b border-neutral-100 dark:border-white/5 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900 dark:text-white">បន្ថែមសាស្ត្រាចារ្យថ្មី <span class="text-sm font-normal text-neutral-400 font-mono">(Add New Lecturer)</span></h3>
            </div>

            <!-- Close Cross Button -->
            <button type="button" onclick="toggleModal()" class="p-1.5 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- 3. Form Component Body (Matches JavaScript Form Selectors Exactly) -->
        <form id="addlecturerForm" class="space-y-4">

            <!-- Input 1: Khmer Name -->
            <div>
                <label for="name_kh" class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">ឈ្មោះជាភាសាខ្មែរ (Khmer Name) <span class="text-rose-500">*</span></label>
                <input type="text" id="name_kh" name="name_kh" required placeholder="ឧ. ឡុង វិសាល"
                       class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950/50 text-neutral-900 dark:text-white border border-neutral-200 dark:border-white/5 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <!-- Input 2: English Name -->
            <div>
                <label for="name_en" class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">ឈ្មោះជាភាសាអង់គ្លេស (English Name) <span class="text-rose-500">*</span></label>
                <input type="text" id="name_en" name="name_en" required placeholder="e.g., Long Visal"
                       class="w-full px-3 py-2 text-sm font-mono bg-neutral-50 dark:bg-neutral-950/50 text-neutral-900 dark:text-white border border-neutral-200 dark:border-white/5 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <!-- Input 3: Code Identification -->
            <div>
                <label for="code" class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">លេខកូដសាស្ត្រាចារ្យ (Lecturer Code)</label>
                <input type="text" id="code" name="code" placeholder="e.g., LEC-001"
                       class="w-full px-3 py-2 text-sm font-mono bg-neutral-50 dark:bg-neutral-950/50 text-neutral-900 dark:text-white border border-neutral-200 dark:border-white/5 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <!-- Input 4: Remark Description -->
            <div>
                <label for="remark" class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">ចំណាំ (Remarks)</label>
                <textarea id="remark" name="remark" rows="3" placeholder="Additional workflow or status details..."
                          class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950/50 text-neutral-900 dark:text-white border border-neutral-200 dark:border-white/5 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all resize-none"></textarea>
            </div>

            <!-- Action Trigger Footer Buttons -->
            <div class="flex justify-end gap-3 border-t border-neutral-100 dark:border-white/5 pt-4 mt-6">
                <button type="button" onclick="toggleModal()"
                        class="px-4 py-2 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-xl transition-all active:scale-95">
                    បោះបង់ (Cancel)
                </button>
                <button type="submit"
                        class="inline-flex items-center px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                    រក្សាទុក (Save Record)
                </button>
            </div>
        </form>
    </div>
</div>
