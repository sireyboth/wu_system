<!-- 1. The Main Modal Container Backdrop Overlay -->
<div id="lecturerModal"
     class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4">

    <!-- 2. The Modal Content Card (The Spring-Pop Target) -->
    <div id="modalCard"
         class="w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-white dark:bg-neutral-900">
            <h3 id="modalTitle" class="text-lg font-bold text-neutral-900 dark:text-white">បន្ថែមសាស្ត្រាចារ្យថ្មី</h3>
            <button type="button" onclick="window.toggleModal()" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Structure -->
        <form id="addlecturerForm" class="p-6 space-y-5 bg-white dark:bg-neutral-900">

            <!-- Input Row: Khmer Name -->
            <div class="relative group">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះភាសាខ្មែរ (Khmer Name)</label>
                <input type="text" name="name_kh" autocomplete="off" placeholder="ឧ. សុខ ម៉ានី"
                       data-hint="សូមបញ្ចូលឈ្មោះជាភាសាខ្មែរឲ្យបានត្រឹមត្រូវ (ឧ. សុខ ម៉ានី)"
                       class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
                <!-- Smart Hint Element Container -->
                <div class="smart-hint pointer-events-none absolute right-3 -top-2 opacity-0 scale-95 translate-y-1 bg-indigo-600 text-white text-[11px] px-2.5 py-1 rounded-md shadow-md transition-all duration-200 font-medium"></div>
            </div>

            <!-- Input Row: English Name -->
            <div class="relative group">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះភាសាអង់គ្លេស (English Name)</label>
                <input type="text" name="name_en" autocomplete="off" placeholder="e.g., SOK Many"
                       data-hint="Capitalize letters correctly (e.g., SOK Many)"
                       class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
                <div class="smart-hint pointer-events-none absolute right-3 -top-2 opacity-0 scale-95 translate-y-1 bg-indigo-600 text-white text-[11px] px-2.5 py-1 rounded-md shadow-md transition-all duration-200 font-medium"></div>
            </div>

            <!-- Input Row: Code -->
            <div class="relative group">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">កូដសាស្ត្រាចារ្យ (Lecturer Code)</label>
                <input type="text" name="code" autocomplete="off" placeholder="e.g., LECT-2026-01"
                       data-hint="Unique code allocation (e.g., LECT-2026-01)"
                       class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none">
                <div class="smart-hint pointer-events-none absolute right-3 -top-2 opacity-0 scale-95 translate-y-1 bg-indigo-600 text-white text-[11px] px-2.5 py-1 rounded-md shadow-md transition-all duration-200 font-medium"></div>
            </div>

            <!-- Input Row: Remark -->
            <div class="relative group">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">សម្គាល់ (Remarks)</label>
                <textarea name="remark" rows="2" placeholder="Enter optional notes here..."
                          data-hint="Optional contextual notes or assignments details"
                          class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white dark:focus:bg-neutral-900 transition-all duration-200 outline-none resize-none"></textarea>
                <div class="smart-hint pointer-events-none absolute right-3 -top-2 opacity-0 scale-95 translate-y-1 bg-indigo-600 text-white text-[11px] px-2.5 py-1 rounded-md shadow-md transition-all duration-200 font-medium"></div>
            </div>

            <!-- Footer Action Controls -->
            <div class="flex justify-end items-center gap-3 pt-4 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900">
                <button type="button" onclick="window.toggleModal()"
                        class="px-4 py-2 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
                    បោះបង់
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-indigo-500/20 active:scale-95 rounded-xl transition-all duration-200">
                    រក្សាទុក
                </button>
            </div>
        </form>
    </div>
</div>
