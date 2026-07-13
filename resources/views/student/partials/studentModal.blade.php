<div id="studentModal"
    class="fixed inset-0 z-50 invisible opacity-0 bg-neutral-900/40 dark:bg-black/60 backdrop-blur-sm transition-all duration-300 items-center justify-center p-4 flex">

    <div id="modalCard"
        class="w-full max-w-7xl h-auto bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-100 dark:border-white/5 transform scale-90 opacity-0 transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] overflow-hidden">

        <div
            class="px-8 py-5 border-b border-neutral-100 dark:border-white/5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-neutral-900">
            <div>
                <h3 id="modalTitle" class="text-xl font-bold text-neutral-900 dark:text-white">
                    ប្រព័ន្ធគ្រប់គ្រងព័ត៌មាននិស្សិត (Comprehensive Student Registration)</h3>
                <p class="text-xs text-neutral-400 mt-0.5">សូមបំពេញព័ត៌មានលម្អិតទាំងអស់ខាងក្រោម
                    ដើម្បីបញ្ចូលទៅក្នុងប្រព័ន្ធទិន្នន័យ</p>
            </div>

            <div
                class="flex flex-wrap bg-neutral-100 dark:bg-neutral-950 p-1 rounded-xl border border-neutral-200/50 dark:border-white/5">
                <button type="button" onclick="switchTab('identity')" id="tab-identity"
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all bg-white dark:bg-neutral-900 text-indigo-600 dark:text-white shadow-sm">
                    ១. អត្តសញ្ញាណផ្ទាល់ខ្លួន
                </button>
                <button type="button" onclick="switchTab('academic')" id="tab-academic"
                    class="px-4 py-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 rounded-lg transition-all hover:text-neutral-900 dark:hover:text-white">
                    ២. ព័ត៌មានសិក្សា
                </button>
                <button type="button" onclick="switchTab('guardian')" id="tab-guardian"
                    class="px-4 py-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 rounded-lg transition-all hover:text-neutral-900 dark:hover:text-white">
                    ៣. ព័ត៌មានអាណាព្យាបាល
                </button>
            </div>
        </div>

        <form id="studentForm" novalidate
            class="p-8 space-y-6 bg-white dark:bg-neutral-900 max-h-[calc(100vh-14rem)] overflow-y-auto scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">

            @include('student.partials.student-info')
            @include('student.partials.student-edu')
            @include('student.partials.student-related')

            <div
                class="flex justify-end items-center gap-3 pt-5 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900 sticky bottom-0 z-10">
                <button type="button" onclick="toggleModal(false)"
                    class="px-5 py-2.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
                    បោះបង់ (Cancel)
                </button>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 active:scale-95 rounded-xl transition-all duration-200">
                    រក្សាទុកទិន្នន័យ (Save Student Record)
                </button>
            </div>
        </form>
    </div>
</div>
