<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-neutral-950/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-neutral-900 rounded-2xl max-w-6xl w-full border border-neutral-200 dark:border-white/10 shadow-2xl flex flex-col max-h-[95vh] overflow-hidden transform scale-95 transition-all duration-200">

        <div class="px-8 py-5 border-b border-neutral-100 dark:border-white/5 flex items-center justify-between bg-neutral-50 dark:bg-neutral-800/20">
            <div>
                <h3 class="text-xl font-bold text-neutral-900 dark:text-white">ព័ត៌មានលម្អិតនិស្សិត (Full Student Profile)</h3>
                <p class="text-sm text-neutral-400 mt-1">ព័ត៌មានយោងគ្រប់ជ្រុងជ្រោយដែលបានរក្សាទុកក្នុងប្រព័ន្ធ</p>
            </div>
            <button type="button" data-close-modal="preview"  class="p-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-8 overflow-y-auto space-y-8 text-base text-neutral-700 dark:text-neutral-300" id="previewModalContent">
            </div>

       <div class="px-8 py-5 border-t border-neutral-100 dark:border-white/5 flex justify-end bg-neutral-50 dark:bg-neutral-800/20">
            <button type="button" data-close-modal="preview" class="px-6 py-2.5 text-base font-semibold text-neutral-700 dark:text-neutral-300 bg-neutral-100 dark:bg-white/5 rounded-xl hover:bg-neutral-200 dark:hover:bg-white/10 transition-colors">បិទ (Close)</button>
        </div>
    </div>
</div>
