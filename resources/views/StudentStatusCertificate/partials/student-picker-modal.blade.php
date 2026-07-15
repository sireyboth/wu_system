{{--
    Student Picker Modal
    ─────────────────────────────────────────────────────────────
    Usage:  @include('probisional.partials.student-picker-modal')
    JS:     resources/js/probisional/student-picker.js
    Trigger: AppModal.toggle(true)
--}}

<div
    id="studentPickerModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="pickerModalTitle"
    class="invisible fixed inset-0 z-50 flex items-center justify-center p-4
           bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"
>
    {{-- ── Card ── --}}
    <div
        id="pickerModalCard"
        class="relative w-full max-w-5xl bg-white dark:bg-neutral-900
               border border-neutral-200 dark:border-white/10
               rounded-2xl shadow-2xl shadow-black/20
               flex flex-col max-h-[90vh]
               scale-95 opacity-0 transition-all duration-300"
    >

        {{-- ── Header ── --}}
        <div class="flex items-center justify-between px-6 py-5
                    border-b border-neutral-100 dark:border-white/5 shrink-0">
            <div>
                <p class="text-xs font-semibold tracking-widest uppercase
                           text-indigo-500 dark:text-indigo-400 mb-0.5">
                    សញ្ញាបត្របណ្ដោះអាសន្ន
                </p>
                <h2 id="pickerModalTitle"
                    class="text-lg font-bold text-neutral-900 dark:text-white">
                    ជ្រើសរើសសិស្ស
                    <span class="text-neutral-400 dark:text-neutral-500 font-normal text-base">
                        — Select a student to begin
                    </span>
                </h2>
            </div>

            <button
                id="pickerCloseBtn"
                class="p-2 rounded-xl text-neutral-400 hover:text-neutral-700
                       hover:bg-neutral-100 dark:hover:bg-white/5
                       dark:hover:text-white transition-colors"
                aria-label="Close"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Search bar ── --}}
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-white/5 shrink-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                    </svg>
                </div>
                <input
                    id="pickerSearchInput"
                    type="text"
                    placeholder="ស្វែងរកតាម​ ឈ្មោះ, Student ID, ជំនាញ…"
                    autocomplete="off"
                    class="w-full pl-9 pr-4 py-2.5 text-sm
                           bg-neutral-50 dark:bg-neutral-800
                           border border-neutral-200 dark:border-white/10
                           rounded-xl text-neutral-900 dark:text-white
                           placeholder-neutral-400 dark:placeholder-neutral-500
                           focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                           transition-all"
                />
            </div>
        </div>

        {{-- ── Table ── --}}
        <div class="relative flex-1 overflow-hidden">

            {{-- Loader --}}
            <div id="pickerLoader"
                 class="hidden absolute inset-0 z-10 flex items-center justify-center
                        bg-white/60 dark:bg-neutral-900/60 backdrop-blur-[2px]">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>

            <div class="overflow-y-auto h-full
                        scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">
                <table class="w-full text-sm text-left">
                    <thead class="sticky top-0 z-10 text-xs uppercase font-bold tracking-wider
                                  text-neutral-500 dark:text-neutral-400
                                  bg-neutral-50/95 dark:bg-neutral-800/95
                                  backdrop-blur-md border-b border-neutral-200 dark:border-white/5">
                        <tr>
                            <th class="px-4 py-3 w-12">N.O</th>
                            <th class="px-4 py-3">ឈ្មោះ / Name</th>
                            <th class="px-4 py-3 w-20">ភេទ</th>
                            <th class="px-4 py-3">ថ្ងៃខែឆ្នាំកំណើត</th>
                            <th class="px-4 py-3">Student ID</th>
                            <th class="px-4 py-3">ជំនាញ / Major</th>
                            <th class="px-4 py-3">ជំនាន់ / Batch</th>
                        </tr>
                    </thead>
                    <tbody id="picker-table-body"
                           class="divide-y divide-neutral-100 dark:divide-white/5">
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-neutral-400">
                                Loading…
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Footer ── --}}
        <div class="flex items-center justify-between px-6 py-4
                    border-t border-neutral-100 dark:border-white/5
                    bg-neutral-50/80 dark:bg-neutral-800/50 rounded-b-2xl shrink-0">

            {{-- Selection preview --}}
            <div id="pickerSelectionPreview" class="text-sm text-neutral-500 dark:text-neutral-400">
                <span class="italic">គ្មានសិស្សត្រូវបានជ្រើសរើស</span>
            </div>

            <div class="flex items-center gap-3">
                <button
                    id="pickerCancelBtn"
                    class="px-4 py-2 text-sm font-medium rounded-xl
                           text-neutral-600 dark:text-neutral-300
                           bg-white dark:bg-neutral-800
                           border border-neutral-200 dark:border-white/10
                           hover:bg-neutral-100 dark:hover:bg-white/5
                           transition-colors"
                >
                    បោះបង់
                </button>

                <button
                    id="pickerBeginBtn"
                    disabled
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-bold rounded-xl
                           text-white bg-indigo-600 shadow-lg shadow-indigo-500/30
                           hover:bg-indigo-700 active:scale-95
                           disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none
                           disabled:active:scale-100 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/>
                    </svg>
                    បញ្ចូលពត៌មាន (Entry Data)
                </button>
            </div>
        </div>
    </div>
</div>
