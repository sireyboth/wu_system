<div id="certificateReportModal"
    class="fixed inset-0 z-50 items-center justify-center hidden px-6 bg-neutral-950/60 backdrop-blur-md">
    <div class="relative w-full max-w-2xl overflow-hidden border shadow-2xl bg-white dark:bg-neutral-900 rounded-3xl border-neutral-200 dark:border-white/10">

        {{-- Header --}}
        <div class="relative px-8 pt-8 pb-12 overflow-hidden bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600">
            <div class="absolute rounded-full -top-10 -right-10 w-48 h-48 bg-white/10 blur-2xl"></div>
            <div class="absolute w-40 h-40 rounded-full -bottom-12 -left-8 bg-white/10 blur-2xl"></div>

            <div class="relative flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex items-center justify-center w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6h6v6m-9 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-2xl font-bold leading-tight text-white">របាយការណ៍សញ្ញាបត្រ</h2>
                        <p class="text-sm font-medium text-white/80">Certificate Report Overview</p>
                    </div>
                </div>
                <button type="button" id="closeCertificateReportBtn"
                    class="p-2 rounded-xl text-white/70 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Progress ring + total --}}
        <div class="flex items-center gap-6 px-8 -mt-12">
            <div id="reportRing"
                class="relative flex items-center justify-center w-32 h-32 rounded-full shrink-0 shadow-xl shadow-indigo-500/20 ring-4 ring-white dark:ring-neutral-900"
                style="background: conic-gradient(#6366f1 0deg, #e5e7eb 0deg);">
                <div class="absolute flex flex-col items-center justify-center bg-white rounded-full dark:bg-neutral-900 w-24 h-24">
                    <span id="reportPrintedPercent" class="text-2xl font-black text-neutral-900 dark:text-white">—</span>
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-wide">Printed</span>
                </div>
            </div>

            <div class="flex-1 pt-12">
                <p class="text-sm font-bold tracking-wide uppercase text-neutral-400">សរុប (Total Certificates)</p>
                <p id="reportTotal" class="text-4xl font-black leading-tight text-neutral-900 dark:text-white">—</p>
            </div>
        </div>

        {{-- Stat tiles --}}
        <div class="grid grid-cols-2 gap-4 px-8 pt-6 pb-8">
            <div class="relative p-6 overflow-hidden border rounded-2xl bg-gradient-to-br from-rose-50 to-white dark:from-rose-500/10 dark:to-white/0 border-rose-200/70 dark:border-rose-500/20">
                <span class="absolute flex items-center justify-center w-10 h-10 rounded-full -top-2 -right-2 bg-rose-100 dark:bg-rose-500/20">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <p class="text-sm font-semibold text-rose-500">មិនទាន់ព្រីន</p>
                <p class="text-sm text-neutral-400 -mt-0.5">Pending</p>
                <p id="reportPending" class="mt-2 text-3xl font-black text-rose-600 dark:text-rose-400">—</p>
            </div>

            <div class="relative p-6 overflow-hidden border rounded-2xl bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-500/10 dark:to-white/0 border-indigo-200/70 dark:border-indigo-500/20">
                <span class="absolute flex items-center justify-center w-10 h-10 rounded-full -top-2 -right-2 bg-indigo-100 dark:bg-indigo-500/20">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <p class="text-sm font-semibold text-indigo-500">បានព្រីនរួច</p>
                <p class="text-sm text-neutral-400 -mt-0.5">Printed</p>
                <p id="reportPrinted" class="mt-2 text-3xl font-black text-indigo-600 dark:text-indigo-400">—</p>
            </div>

            <div class="col-span-2 flex items-center justify-between p-5 border rounded-2xl bg-neutral-50 dark:bg-white/5 border-neutral-200 dark:border-white/10">
                <div>
                    <p class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">ផ្សេងៗ</p>
                    <p class="text-sm text-neutral-400 -mt-0.5">Other statuses</p>
                </div>
                <p id="reportOther" class="text-2xl font-black text-neutral-800 dark:text-neutral-100">—</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-8 py-5 border-t border-neutral-200 dark:border-white/10 bg-neutral-50 dark:bg-white/5">
            <button type="button" id="closeCertificateReportBtn2"
                class="px-6 py-2.5 text-base font-semibold transition-all rounded-xl text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/10">
                បិទ (Close)
            </button>
        </div>
    </div>
</div>
