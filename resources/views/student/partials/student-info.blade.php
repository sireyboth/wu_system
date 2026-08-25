<div id="panel-identity" class="tab-panel space-y-5">
    <div class="rounded-2xl border border-neutral-100 dark:border-white/5 bg-neutral-50/60 dark:bg-white/[0.02] p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 21v-1a7 7 0 0 1 14 0v1"></path>
                </svg>
            </span>
            <h4 class="text-sm font-bold text-neutral-800 dark:text-white">
                អត្តសញ្ញាណផ្ទាល់ខ្លួនរបស់និស្សិត (Student Core Personal Identity)</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="relative group">
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">នាមត្រកូល
                    (First Name)</label>
                <input required type="text" name="first_name_kh" placeholder="ឧ. សុខ"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>

            <div class="relative group">
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">គោត្តនាម
                    (Last Name)</label>
                <input required type="text" name="last_name_kh" placeholder="ឧ. ភារម្យ"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>

            <div class="relative group">
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">នាមត្រកូល
                    (First Name Latin)</label>
                <input required type="text" name="first_name" placeholder="e.g., SOK"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
            <div class="relative group">
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">គោត្តនាម
                    (Last Name Latin)</label>
                <input required type="text" name="last_name" placeholder="e.g., PHEAROM"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
            <div class="relative group">
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ភេទ
                    (Gender)</label>
                <select required name="sex"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none appearance-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                    <option value="male">ប្រុស (Male)</option>
                    <option value="female">ស្រី (Female)</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">សញ្ជាតិនិស្សិត
                    (Student Nationality)</label>
                <select required name="nationality_id" id="student-nationality"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសសញ្ជាតិ --</option>
                </select>
            </div>
            <div class="relative group">
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ថ្ងៃខែឆ្នាំកំណើត
                    (Date of Birth)</label>
                <input required type="date" name="dob"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លេខសញ្ញាបត្របាក់ឌុប
                    (BACC II Exam Seat ID)</label>
                <input type="text" name="bacc_2_code" placeholder="e.g., 99402A"
                    class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>

        </div>
    </div>

    <div class="rounded-2xl border border-neutral-100 dark:border-white/5 bg-neutral-50/60 dark:bg-white/[0.02] p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                </svg>
            </span>
            <h4 class="text-sm font-bold text-neutral-800 dark:text-white">
                កម្រិតសិក្សា និងទំនាក់ទំនង (Academic Level & Contact)</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">កម្រិតសិក្សា
                    (Degree Type) <span class="text-rose-500">*</span></label>
                <select required name="degree_type" id="student_degree_type"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors">
                    <option value="" disabled selected>-- ជ្រើសរើសកម្រិតសិក្សា --</option>
                    <option value="associate">បរិញ្ញាបត្ររង (Associate Degree)</option>
                    <option value="bachelor">បរិញ្ញាបត្រ (Bachelor Degree)</option>
                    <option value="master">បរិញ្ញាបត្រជាន់ខ្ពស់ (Master Degree)</option>
                    <option value="phd">បណ្ឌិត (PhD)</option>
                </select>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">លេខទូរស័ព្ទនិស្សិត
                    (Student Phone Number)</label>
                <input type="tel" name="phones[0]" placeholder="e.g., 012345678"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អ៊ីមែល
                    (Email Address)</label>
                <input type="email" name="email" placeholder="e.g., student@university.edu.kh"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
            </div>
        </div>
    </div>

    {{-- Address --}}
    <div class="rounded-2xl border border-neutral-100 dark:border-white/5 bg-neutral-50/60 dark:bg-white/[0.02] p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-600/10 text-emerald-600 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </span>
            <h4 class="text-sm font-bold text-neutral-800 dark:text-white">
                អាសយដ្ឋានបច្ចុប្បន្នរបស់និស្សិត (Student Current Address)</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ផ្ទះ
                    / ផ្លូវ (Street/House)</label>
                <input  type="text" name="addresses[0][street]" placeholder="លំនៅដ្ឋាន លេខផ្ទះ ឬផ្លូវ"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ខេត្ត
                    / រាជធានី</label>
                <select required name="addresses[0][province_id]" id="student_province"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសខេត្ត --</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ស្រុក
                    / ខណ្ឌ</label>
                <select required name="addresses[0][district_id]" id="student_district" disabled
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសស្រុក --</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឃុំ
                    / សង្កាត់</label>
                <select required name="addresses[0][commune_id]" id="student_commune" disabled
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសឃុំ --</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ភូមិ</label>
                <select required name="addresses[0][village_id]" id="student_village" disabled
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសភូមិ --</option>
                </select>
            </div>
            <input type="hidden" name="addresses[0][type]" value="current">
        </div>
    </div>

    <div class="rounded-2xl border border-neutral-100 dark:border-white/5 bg-neutral-50/60 dark:bg-white/[0.02] p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-600/10 text-amber-600 dark:text-amber-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"></path>
                    <circle cx="12" cy="10" r="2.5"></circle>
                </svg>
            </span>
            <h4 class="text-sm font-bold text-neutral-800 dark:text-white">
                ទីកន្លែងកំណើតរបស់និស្សិត (Student Place of Birth)</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ខេត្ត
                    / រាជធានី</label>
                <select required name="addresses[1][province_id]" id="student-province"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសខេត្ត --</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ស្រុក
                    / ខណ្ឌ</label>
                <select required name="addresses[1][district_id]" id="student-district" disabled
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសស្រុក --</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឃុំ
                    / សង្កាត់</label>
                <select required name="addresses[1][commune_id]" id="student-commune" disabled
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសឃុំ --</option>
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ភូមិ</label>
                <select required name="addresses[1][village_id]" id="student-village" disabled
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none">
                    <option value="" disabled selected>-- ជ្រើសរើសភូមិ --</option>
                </select>
            </div>
            <input type="hidden" name="addresses[1][type]" value="birth">
        </div>
    </div>

    <div class="w-full">
        <label
            class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">កំណត់សម្គាល់ផ្សេងៗ
            (Remarks / Notes)</label>
        <textarea name="remark" rows="3"
            placeholder="បញ្ចូលព័ត៌មានបន្ថែម ឬកំណត់សម្គាល់ផ្សេងៗទីនេះ... (Enter additional student remarks or administrative logs here...)"
            class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all resize-none"></textarea>
    </div>
</div>
