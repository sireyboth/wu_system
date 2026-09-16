<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-neutral-950/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-neutral-900 rounded-2xl max-w-5xl w-full border border-neutral-200 dark:border-white/10 shadow-2xl flex flex-col max-h-[92vh] overflow-hidden transform scale-95 transition-all duration-200">

        <button type="button" data-close-modal="preview" class="absolute top-5 right-5 z-10 p-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-white/10 rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div id="previewModalContent" class="overflow-y-auto">
            </div>

        <div class="px-8 py-4 border-t border-neutral-100 dark:border-white/5 flex justify-end bg-neutral-50 dark:bg-neutral-900 shrink-0">
            <button type="button" data-close-modal="preview" class="px-6 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 bg-neutral-100 dark:bg-white/10 rounded-xl hover:bg-neutral-200 dark:hover:bg-white/20 transition-colors">បិទ (Close)</button>
        </div>
    </div>
</div>
