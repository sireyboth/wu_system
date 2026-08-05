<div id="certificateReportModal"
    class="fixed inset-0 z-50 items-center justify-center hidden px-4 bg-black/40 backdrop-blur-sm">
    <div class="w-full max-w-md overflow-hidden bg-white border shadow-2xl dark:bg-neutral-900 rounded-2xl border-neutral-200 dark:border-white/10">

        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-white/10">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-white">របាយការណ៍សញ្ញាបត្រ (Certificate Report)</h2>
            <button type="button" id="closeCertificateReportBtn"
                class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 dark:hover:text-white dark:hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3 px-6 py-5">
            <div class="p-4 border rounded-xl bg-neutral-50 dark:bg-white/5 border-neutral-200 dark:border-white/10">
                <p class="text-xs text-neutral-400">សរុប (Total)</p>
                <p id="reportTotal" class="text-2xl font-bold text-neutral-900 dark:text-white">—</p>
            </div>
            <div class="p-4 border rounded-xl bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/20">
                <p class="text-xs text-rose-500">មិនទាន់ព្រីន (Pending)</p>
                <p id="reportPending" class="text-2xl font-bold text-rose-600 dark:text-rose-400">—</p>
            </div>
            <div class="p-4 border rounded-xl bg-indigo-50 dark:bg-indigo-500/10 border-indigo-200 dark:border-indigo-500/20">
                <p class="text-xs text-indigo-500">បានព្រីនរួច (Printed)</p>
                <p id="reportPrinted" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">—</p>
            </div>
            <div class="p-4 border rounded-xl bg-neutral-50 dark:bg-white/5 border-neutral-200 dark:border-white/10">
                <p class="text-xs text-neutral-400">ផ្សេងៗ (Other)</p>
                <p id="reportOther" class="text-2xl font-bold text-neutral-900 dark:text-white">—</p>
            </div>
        </div>

        <div class="flex items-center justify-end px-6 py-4 border-t border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5">
            <button type="button" id="closeCertificateReportBtn2"
                class="px-4 py-2 text-sm font-medium transition-all rounded-lg text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/10">
                បិទ (Close)
            </button>
        </div>
    </div>
</div>
