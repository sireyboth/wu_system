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

        <form id="studentForm" novalidate class="p-8 space-y-6 bg-white dark:bg-neutral-900 max-h-[calc(100vh-14rem)] overflow-y-auto scrollbar-thin scrollbar-thumb-neutral-200 dark:scrollbar-thumb-white/10">

            <div id="panel-identity" class="tab-panel space-y-6">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">អត្តសញ្ញាណផ្ទាល់ខ្លួនរបស់និស្សិត (Student Core Personal Identity)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="relative group">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">នាមត្រកូល (First Name)</label>
                            <input required type="text" name="first_name" placeholder="ឧ. សុខ ភារម្យ" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">គោត្តនាម (Last Name)</label>
                            <input required type="text" name="last_name" placeholder="e.g., SOK PHEAROM" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">នាមត្រកូល (First Name)</label>
                            <input required type="text" name="first_name_kh" placeholder="ឧ. សុខ ភារម្យ" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">គោត្តនាម (Last Name)</label>
                            <input required type="text" name="last_name_kh" placeholder="e.g., SOK PHEAROM" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ភេទ (Gender)</label>
                            <select required name="sex" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none appearance-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                                <option value="male">ប្រុស (Male)</option>
                                <option value="female">ស្រី (Female)</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">សញ្ជាតិនិស្សិត (Student Nationality) <span class="text-rose-500">*</span></label>
                            <select required name="nationality_id" id="student_nationality" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-colors">
                                <option value="" disabled selected>-- ជ្រើសរើសសញ្ជាតិ --</option>
                            </select>
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃខែឆ្នាំកំណើត (Date of Birth)</label>
                            <input required type="date" name="dob" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លេខទូរស័ព្ទនិស្សិត (Student Phone Number)</label>
                        <input required type="tel" name="phone" placeholder="e.g., 012345678" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អ៊ីមែល (Email Address)</label>
                        <input type="email" name="email" placeholder="e.g., student@university.edu.kh" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </div>
                </div>

                <div class="pt-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">អាសយដ្ឋានបច្ចុប្បន្នរបស់និស្សិត (Student Current Address)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ផ្ទះ / ផ្លូវ (Street/House)</label>
                            <input required type="text" name="addresses[0][street]" placeholder="លំនៅដ្ឋាន លេខផ្ទះ ឬផ្លូវ" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ខេត្ត / រាជធានី</label>
                            <select required name="addresses[0][province_id]" id="student_province" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                                <option value="" disabled selected>-- ជ្រើសរើសខេត្ត --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ស្រុក / ខណ្ឌ</label>
                            <select required name="addresses[0][district_id]" id="student_district" disabled class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                                <option value="" disabled selected>-- ជ្រើសរើសស្រុក --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឃុំ / សង្កាត់</label>
                            <select required name="addresses[0][commune_id]" id="student_commune" disabled class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                                <option value="" disabled selected>-- ជ្រើសរើសឃុំ --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ភូមិ</label>
                            <select required name="addresses[0][village_id]" id="student_village" disabled class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                                <option value="" disabled selected>-- ជ្រើសរើសភូមិ --</option>
                            </select>
                        </div>
                        <input type="hidden" name="addresses[0][type]" value="current">
                    </div>
                </div>
            </div>

            <div id="panel-academic" class="tab-panel hidden space-y-6">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">ព័ត៌មានរដ្ឋបាលសិក្សា (Institutional Academic Routing)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អត្តលេខនិស្សិត (Student Identification Code)</label>
                            <input required type="text" name="code" placeholder="e.g., ST-2026-048" class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ជំនាន់សិក្សា (Batch)</label>
                            <select required name="batch_id" id="academic_batch_id" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                                <option value="" disabled selected>-- ជ្រើសរើសជំនាន់ --</option>
                            </select>
                        </div>
                                                <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ជំនាញឯកទេស (Major)</label>
                            <select required name="major_id" id="academic_major_id" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                                <option value="" disabled selected>-- ជ្រើសរើសជំនាញ --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ស្ថានភាពការសិក្សា (Enrollment Lifecycle Status)</label>
                            <select name="status" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                                <option value="active">កំពុងសិក្សា (Active)</option>
                                <option value="suspended">ព្យួរការសិក្សា (Suspended)</option>
                                <option value="graduated">បញ្ចប់ការសិក្សា (Graduated)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">កាលបរិច្ឆេទ និងលទ្ធផលវាយតម្លៃ (Metrics & Admission Timelines)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃចូលរៀនដំបូង (Official Admission Date)</label>
                            <input required type="date" name="admission_at" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លេខតុប្រឡងបាក់ឌុប (BACC II Exam Seat ID)</label>
                            <input type="text" name="bacc_2_code" placeholder="e.g., 99402A" class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លទ្ធផលប្រឡងចម្រាញ់ចូល (Entrance Result)</label>
                            <select name="entrance_exam" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors">
                                <option value="" disabled selected>-- ជ្រើសរើសលទ្ធផល --</option>
                                <option value="1">ជាប់ (Passed)</option>
                                <option value="2">ធ្លាក់ (Failed)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លទ្ធផលប្រឡងបញ្ចប់ការសិក្សា (Exit Result)</label>
                            <select name="exit_exam" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors">
                                <option value="" disabled selected>-- ជ្រើសរើសលទ្ធផល --</option>
                                <option value="1">ជាប់ (Passed)</option>
                                <option value="2">ធ្លាក់ (Failed)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="panel-guardian" class="tab-panel hidden space-y-6">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-4 pb-1 border-b border-neutral-100 dark:border-white/5">ព័ត៌មានផ្ទាល់ខ្លួនអាណាព្យាបាល (Guardian Core Profile & Identity)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះអាណាព្យាបាលជាភាសាខ្មែរ</label>
                            <input required type="text" name="guardians[0][first_name]" placeholder="ឧ. សុខ ហេង" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះអាណាព្យាបាលជាភាសាអង់គ្លេស</label>
                            <input required type="text" name="guardians[0][last_name]" placeholder="e.g., SOK HENG" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះអាណាព្យាបាលជាភាសាខ្មែរ</label>
                            <input required type="text" name="guardians[0][first_name_kh]" placeholder="ឧ. សុខ ហេង" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះអាណាព្យាបាលជាភាសាអង់គ្លេស</label>
                            <input required type="text" name="guardians[0][last_name_kh]" placeholder="e.g., SOK HENG" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ភេទ (Guardian Gender)</label>
                            <select required name="guardians[0][sex]" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                                <option value="male">ប្រុស (Male)</option>
                                <option value="female">ស្រី (Female)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃខែឆ្នាំកំណើតអាណាព្យាបាល</label>
                            <input type="date" name="guardians[0][dob]" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        </div>
                       <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">សញ្ជាតិអាណាព្យាបាល (Guardian Nationality) <span class="text-rose-500">*</span></label>
                            <select required name="guardians[0][nationality_id]" id="guardian_nationality" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-colors">
                                <option value="" disabled selected>-- ជ្រើសរើសសញ្ជាតិ --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ត្រូវជា (Relationship Connection)</label>
                        <select name="guardians[0][relationship]" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                            <option value="father">ឪពុក (Father)</option>
                            <option value="mother">ម្តាយ (Mother)</option>
                            <option value="other">អាណាព្យាបាល (Other)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">មុខរបរ (Guardian Occupation)</label>
                        <input type="text" name="guardians[0][occupation]" placeholder="មុខរបរ ឬការងារបច្ចុប្បន្ន" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លេខទូរស័ព្ទទំនាក់ទំនងអាណាព្យាបាល</label>
                        <input required type="tel" name="guardians[0][phone]" placeholder="e.g., 012778899" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អ្នកទំនាក់ទំនងចម្បង (Is Primary Contact?)</label>
                        <select name="guardians[0][is_primary]" class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                            <option value="1">បាទ/ចាស (Primary Contact)</option>
                            <option value="0">ទេ (Secondary Contact)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center gap-3 pt-5 border-t border-neutral-100 dark:border-white/5 bg-white dark:bg-neutral-900 sticky bottom-0 z-10">
                <button type="button" onclick="toggleModal(false)" class="px-5 py-2.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all duration-200">
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
