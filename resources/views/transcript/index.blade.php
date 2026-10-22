@extends('layouts.dashboard')
@section('title', 'Request Docs')
@section('content')

    @php
        $input = 'w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none';
        $label = 'block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5';
        $section = 'flex items-center gap-2 text-sm font-bold text-neutral-900 dark:text-white';
        $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
    @endphp

    <x-core.page-header title="ស្នើសុំឯកសារ (Request Docs)"
        subtitle="ព្រឹត្តិប័ត្រពិន្ទុ និងវិញ្ញាបនបត្របណ្ដោះអាសន្ន — បោះពុម្ពលើក្រដាសក្បាល (Transcript & Provisional Certificate, printed on letterhead)" />

    {{-- Section badges count only the visible sections (Courses hides for a provisional certificate). --}}
    <style>
        #transcriptForm { counter-reset: step; }
        #transcriptForm .step-no::before { counter-increment: step; content: counter(step); }
    </style>

    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-500 group-focus-within:text-indigo-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
                <input id="transcriptSearchInput" type="text"
                    class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                    placeholder="Search by N°, name, ID or academic year..." autocomplete="off" />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="transcriptReportBtn"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    របាយការណ៍ (Report)
                </button>
                @can('certificate.create')
                    <button type="button" id="transcriptAddBtn"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        បង្កើតឯកសារ (New Request)
                    </button>
                @endcan
            </div>
        </div>

        <x-ui.data-table :headers="[
            'N.O',
            'N° Issue / Type',
            'Student',
            'Major',
            'Credits / GPA',
            'Entrance',
            ['label' => 'Actions', 'align' => 'right'],
        ]" body-id="transcript-table-body" />
    </div>

    {{-- Create / Edit --}}
    <x-ui.modal id="transcriptModal" card-id="transcriptModalCard" title-id="transcriptModalTitle"
        title="បង្កើតឯកសារ (New Request)" form-id="transcriptForm"
        close-fn="TranscriptModal" max-width="max-w-[min(96vw,1680px)]" max-height="max-h-[calc(100vh-4rem)]">

        {{-- Document type — decides the print layout and which sections below show. --}}
        <section class="space-y-4">
            <h4 class="{{ $section }}">
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white step-no"></span>
                ប្រភេទឯកសារ (Document Type)
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($docTypes as $value => $docLabel)
                    <label class="flex items-center gap-3 p-4 rounded-2xl border border-neutral-200 dark:border-white/10 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-500/10 transition-colors">
                        <input type="radio" name="doc_type" value="{{ $value }}" @checked($loop->first) class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                        <span>
                            <span class="block text-sm font-bold text-neutral-900 dark:text-white">
                                {{ $value === 'provisional' ? 'វិញ្ញាបនបត្របណ្ដោះអាសន្ន' : 'ព្រឹត្តិប័ត្រពិន្ទុ' }} ({{ $docLabel }})
                            </span>
                            <span class="block text-xs text-neutral-500">
                                {{ $value === 'provisional' ? 'Bilingual certificate printed inside the pre-printed frame' : 'Courses, credits & GPA by year' }}
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
        </section>

        {{-- Student --}}
        <section class="space-y-4 pt-5 border-t border-neutral-100 dark:border-white/5">
            <h4 class="{{ $section }}">
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white step-no"></span>
                ព័ត៌មានសិស្ស (Student)
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="{{ $label }}">Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required maxlength="100" autocomplete="off" placeholder="e.g. SOPHEAROM" class="{{ $input }} uppercase">
                </div>
                <div>
                    <label class="{{ $label }}">Surname <span class="text-rose-500">*</span></label>
                    <input type="text" name="surname" required maxlength="100" autocomplete="off" placeholder="e.g. NUTH" class="{{ $input }} uppercase">
                </div>
                <div data-only="provisional">
                    <label class="{{ $label }}">Name (Khmer) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_kh" maxlength="150" autocomplete="off" placeholder="e.g. កើតដា សុធាដេត" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">ID</label>
                    <input type="text" name="student_code" maxlength="50" autocomplete="off" placeholder="e.g. 0013385" class="{{ $input }} font-mono">
                </div>
                <div>
                    <label class="{{ $label }}">Sex</label>
                    <select name="sex" class="{{ $input }}">
                        <option value="">— Select —</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Date of Birth</label>
                    <input type="date" name="dob" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Place of Birth</label>
                    {{-- Picking a province also fills the Khmer place of birth (data-kh). --}}
                    <select name="pob" class="{{ $input }}">
                        <option value="">— Select —</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province['en'] }}" data-kh="{{ $province['kh'] }}">{{ $province['en'] }} · {{ $province['kh'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div data-only="provisional">
                    <label class="{{ $label }}">Place of Birth (Khmer)</label>
                    <input type="text" name="pob_kh" maxlength="150" autocomplete="off" placeholder="e.g. ខេត្តកំពង់ឆ្នាំង" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Nationality</label>
                    <select name="nationality" class="{{ $input }}">
                        <option value="">— Select —</option>
                        @foreach ($nationalities as $nationality)
                            <option value="{{ $nationality }}">{{ $nationality }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Address</label>
                    <input type="text" name="address" maxlength="255" autocomplete="off" placeholder="e.g. Phnom Penh" class="{{ $input }}">
                </div>
            </div>
        </section>

        {{-- 2. Program --}}
        <section class="space-y-4 pt-5 border-t border-neutral-100 dark:border-white/5">
            <h4 class="{{ $section }}">
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white step-no"></span>
                កម្មវិធីសិក្សា (Program)
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="{{ $label }}">Degree <span class="text-rose-500">*</span></label>
                    <select name="degree" required class="{{ $input }}">
                        @foreach ($degrees as $degree)
                            <option value="{{ $degree['value'] }}" @selected($degree['value'] === 'bachelor')>{{ $degree['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Major</label>
                    <select name="major_id" class="{{ $input }}">
                        <option value="">— Select —</option>
                        @foreach ($majors as $major)
                            <option value="{{ $major->id }}">{{ $major->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Academic Year</label>
                    <input type="text" name="academic_year" maxlength="50" autocomplete="off" placeholder="e.g. 2022-2026" class="{{ $input }}">
                </div>
                <div data-only="provisional">
                    <label class="{{ $label }}">Comprehensive Exam Date</label>
                    <input type="date" name="exam_date" class="{{ $input }}">
                </div>
            </div>
        </section>

        {{-- Courses — transcript only --}}
        <section data-only="transcript" class="space-y-4 pt-5 border-t border-neutral-100 dark:border-white/5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <h4 class="{{ $section }}">
                    <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white step-no"></span>
                    មុខវិជ្ជា និងពិន្ទុ (Courses &amp; Scores)
                </h4>
                {{-- Live totals --}}
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1.5 rounded-xl bg-neutral-100 dark:bg-white/5 text-xs text-neutral-600 dark:text-neutral-300">
                        Credits Completed: <span id="transcriptTotalCredits" class="font-bold text-neutral-900 dark:text-white">0</span>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-xs text-indigo-700 dark:text-indigo-300">
                        Total GPA: <span id="transcriptTotalGpa" class="font-bold">0.00</span>
                    </div>
                </div>
            </div>

            {{-- Year tabs --}}
            <div class="flex flex-wrap gap-2" id="transcriptYearTabs">
                @foreach ($romans as $year => $roman)
                    <button type="button" data-year-tab="{{ $year }}"
                        class="year-tab inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl border transition-colors">
                        Year {{ $roman }}
                        <span data-year-count="{{ $year }}" class="min-w-5 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-neutral-200 dark:bg-white/10">0</span>
                    </button>
                @endforeach
            </div>

            @foreach ($romans as $year => $roman)
                <div data-year-panel="{{ $year }}" class="hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach ([1 => 'I', 2 => 'II'] as $semester => $semRoman)
                        <div class="p-4 rounded-2xl border border-neutral-200 dark:border-white/10 bg-neutral-50/60 dark:bg-white/[0.02]">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm font-bold text-neutral-800 dark:text-neutral-100">
                                    Year {{ $roman }} · Semester {{ $semRoman }}
                                    <span data-block-count="{{ $year }}-{{ $semester }}" class="ms-1 text-xs font-medium text-neutral-400">0/5</span>
                                </div>
                                <button type="button" data-action="add-course" data-year="{{ $year }}" data-semester="{{ $semester }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                                    Add Subject
                                </button>
                            </div>
                            <div data-course-list="{{ $year }}-{{ $semester }}" class="space-y-2"></div>
                            <p data-course-empty="{{ $year }}-{{ $semester }}"
                                class="text-xs text-center text-neutral-400 py-4 border border-dashed border-neutral-200 dark:border-white/10 rounded-xl">
                                មិនទាន់មានមុខវិជ្ជា (No subjects yet)
                            </p>
                        </div>
                    @endforeach
                </div>
                </div>
            @endforeach
            <p class="text-[11px] text-neutral-400">
                Max 5 subjects per semester. Leave the score empty for <b>IP</b> (in progress) — it prints as IP and is not counted in credits or GPA.
            </p>
        </section>

        {{-- 4. Issue --}}
        <section class="space-y-4 pt-5 border-t border-neutral-100 dark:border-white/5">
            <h4 class="{{ $section }}">
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white step-no"></span>
                ការចេញឯកសារ (Issue)
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="{{ $label }}">N° Issue <span class="text-rose-500">*</span></label>
                    <input type="text" name="issue_no" required maxlength="100" autocomplete="off" placeholder="e.g. 1270/26 WU/R.O" class="{{ $input }} font-mono">
                </div>
                <div>
                    <label class="{{ $label }}">Date (English)</label>
                    <input type="date" name="issue_date" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Date (Khmer · ចន្ទគតិ)</label>
                    <input type="text" name="date_kh" maxlength="255" autocomplete="off" placeholder="Auto from English date" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Person in Charge</label>
                    <input type="text" name="person_in_charge" maxlength="100" autocomplete="off" placeholder="e.g. Kieng Rotana, Ph.D." class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Role</label>
                    <input type="text" name="person_role" maxlength="100" autocomplete="off" placeholder="e.g. PRESIDENT" class="{{ $input }}">
                </div>
                <div data-only="provisional">
                    <label class="{{ $label }}">Person in Charge (Khmer)</label>
                    <input type="text" name="person_in_charge_kh" maxlength="100" autocomplete="off" placeholder="e.g. បណ្ឌិត គៀង រតនា" class="{{ $input }}">
                </div>
                <div data-only="provisional">
                    <label class="{{ $label }}">Role (Khmer)</label>
                    <input type="text" name="person_role_kh" maxlength="100" autocomplete="off" placeholder="e.g. សាកលវិទ្យាធិការ" class="{{ $input }}">
                </div>
            </div>
        </section>

        {{-- 5. Entrance exam + note --}}
        <section class="space-y-4 pt-5 border-t border-neutral-100 dark:border-white/5">
            <h4 class="{{ $section }}">
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white step-no"></span>
                ប្រឡងចូល និងកំណត់សម្គាល់ (Entrance Exam &amp; Note)
            </h4>
            <label class="flex items-start gap-3 p-4 rounded-2xl border border-neutral-200 dark:border-white/10 cursor-pointer">
                <input type="checkbox" name="entrance_exam" value="1" class="mt-0.5 w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500">
                <span>
                    <span class="block text-sm font-bold text-neutral-900 dark:text-white">បានជាប់ប្រឡងចូល (Entrance exam passed)</span>
                    <span class="block text-xs text-neutral-500">If not passed, the document can be saved but <b>cannot be printed</b>.</span>
                </span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}">Entrance Exam Description</label>
                    <textarea name="entrance_exam_description" rows="3" maxlength="2000" class="{{ $input }} resize-none"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Note</label>
                    <textarea name="remark" rows="3" maxlength="2000" class="{{ $input }} resize-none"></textarea>
                </div>
            </div>
        </section>

        <x-slot:footer>
            <button type="button" onclick="TranscriptModal.toggle(false)"
                class="px-4 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-colors">
                បោះបង់ (Cancel)
            </button>
            <button type="button" id="transcriptDraftBtn" title="Save what you have now, finish later — drafts can't be printed"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                រក្សាទុកព្រាង (Save as Draft)
            </button>
            <button type="submit" form="transcriptForm"
                class="px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                រក្សាទុក (Save)
            </button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Fix a subject in the master Subjects list; opened from a course row's "Edit". Comes after the transcript modal so it stacks on top. --}}
    <x-ui.modal id="subjectEditModal" card-id="subjectEditModalCard" title-id="subjectEditModalTitle"
        title="កែសម្រួលមុខវិជ្ជា (Edit Subject)" form-id="subjectEditForm" close-fn="SubjectEditModal" max-width="max-w-xl">
        <div class="flex items-start gap-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-xs text-amber-800 dark:text-amber-300">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span>This changes the subject in the <b>Subjects</b> list. Transcripts already saved keep the old code until you open them and click <b>Save</b>.</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="{{ $label }}">Code <span class="text-rose-500">*</span></label>
                <input type="text" name="code" required maxlength="50" autocomplete="off" class="{{ $input }} font-mono">
            </div>
            <div>
                <label class="{{ $label }}">Credit</label>
                <input type="number" name="credit" min="0" max="30" step="1" class="{{ $input }}">
            </div>
        </div>
        <div>
            <label class="{{ $label }}">Title (English) <span class="text-rose-500">*</span></label>
            <input type="text" name="name_en" required maxlength="100" autocomplete="off" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">Title (Khmer) <span class="text-rose-500">*</span></label>
            <input type="text" name="name_kh" required maxlength="100" autocomplete="off" class="{{ $input }}">
        </div>

        <x-slot:footer>
            <button type="button" onclick="SubjectEditModal.toggle(false)"
                class="px-4 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-colors">
                បោះបង់ (Cancel)
            </button>
            <button type="submit" form="subjectEditForm"
                class="px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                រក្សាទុក (Save Subject)
            </button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Summary report; numbers filled by resources/js/transcript/report.js. --}}
    <x-ui.modal id="transcriptReportModal" card-id="transcriptReportModalCard" title-id="transcriptReportModalTitle"
        title="របាយការណ៍ឯកសារ (Request Docs Report)" close-fn="TranscriptReportModal"
        max-width="max-w-[min(96vw,1280px)]" max-height="max-h-[calc(100vh-4rem)]">

        {{-- Filters — any change reloads the report. --}}
        <div id="transcriptReportFilters" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-3 items-end">
            <div>
                <label class="{{ $label }}">Document Type</label>
                <select name="doc_type" class="{{ $input }}">
                    <option value="">All types</option>
                    @foreach ($docTypes as $value => $docLabel)
                        <option value="{{ $value }}">{{ $docLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Major</label>
                <select name="major_id" class="{{ $input }}">
                    <option value="">All majors</option>
                    @foreach ($majors as $major)
                        <option value="{{ $major->id }}">{{ $major->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Degree</label>
                <select name="degree" class="{{ $input }}">
                    <option value="">All degrees</option>
                    @foreach ($degrees as $degree)
                        <option value="{{ $degree['value'] }}">{{ $degree['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Status</label>
                <select name="status" class="{{ $input }}">
                    <option value="">All statuses</option>
                    <option value="final">Final</option>
                    <option value="draft">Draft</option>
                    <option value="printable">Printable</option>
                    <option value="blocked">Not printable (entrance exam)</option>
                </select>
            </div>
            <div>
                <label class="{{ $label }}">Issue date from</label>
                <input type="date" name="date_from" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">Issue date to</label>
                <input type="date" name="date_to" class="{{ $input }}">
            </div>
            <button type="button" id="transcriptReportReset"
                class="px-4 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 border border-neutral-200 dark:border-white/10 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-colors">
                Reset filters
            </button>
        </div>

        {{-- Headline numbers --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
            @foreach ([
                'total'       => ['សរុប', 'Total', 'text-neutral-900 dark:text-white'],
                'final'       => ['ចុងក្រោយ', 'Final', 'text-neutral-900 dark:text-white'],
                'draft'       => ['សេចក្ដីព្រាង', 'Draft', 'text-neutral-900 dark:text-white'],
                'printable'   => ['អាចបោះពុម្ព', 'Printable', 'text-emerald-700 dark:text-emerald-300'],
                'blocked'     => ['មិនអាចបោះពុម្ព', 'Not printable', 'text-rose-700 dark:text-rose-300'],
                'average_gpa' => ['GPA មធ្យម', 'Average GPA (final)', 'text-indigo-700 dark:text-indigo-300'],
            ] as $key => [$kh, $en, $tone])
                <div class="p-4 rounded-2xl border border-neutral-200 dark:border-white/10 bg-neutral-50/60 dark:bg-white/[0.02]">
                    <p class="text-xs font-bold text-neutral-700 dark:text-neutral-200">{{ $kh }}</p>
                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400">{{ $en }}</p>
                    <p data-report="{{ $key }}" class="mt-2 text-3xl font-black tabular-nums {{ $tone }}">0</p>
                </div>
            @endforeach
        </div>

        {{-- Distributions --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl border border-neutral-200 dark:border-white/10">
                <h4 class="{{ $section }} mb-1">GPA spread</h4>
                <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">Final transcripts by total GPA</p>
                <div data-report-bars="gpa_bands" class="space-y-2.5"></div>
            </div>
            <div class="p-4 rounded-2xl border border-neutral-200 dark:border-white/10">
                <h4 class="{{ $section }} mb-1">By degree</h4>
                <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mb-3">All transcripts in the filter</p>
                <div data-report-bars="by_degree" class="space-y-2.5"></div>
            </div>
        </div>

        {{-- By major --}}
        <div class="rounded-2xl border border-neutral-200 dark:border-white/10 overflow-hidden">
            <div class="px-4 py-3 border-b border-neutral-200 dark:border-white/10">
                <h4 class="{{ $section }}">By major</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-neutral-50 dark:bg-white/[0.03] text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold">Major</th>
                            <th class="px-4 py-2.5 text-right font-semibold">Total</th>
                            <th class="px-4 py-2.5 text-right font-semibold">Final</th>
                            <th class="px-4 py-2.5 text-right font-semibold">Draft</th>
                            <th class="px-4 py-2.5 text-right font-semibold">Not printable</th>
                            <th class="px-4 py-2.5 text-right font-semibold">Avg GPA</th>
                        </tr>
                    </thead>
                    <tbody data-report-table="by_major" class="divide-y divide-neutral-100 dark:divide-white/5"></tbody>
                </table>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" onclick="TranscriptReportModal.toggle(false)"
                class="px-4 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-colors">
                បិទ (Close)
            </button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- One course row; cloned by "Add Subject". --}}
    <template id="transcriptCourseTemplate">
        <div class="course-row relative grid grid-cols-12 gap-2 items-start p-2.5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl">
            <div class="col-span-12 sm:col-span-6 relative">
                <input type="hidden" data-field="subject_id">
                <input type="text" data-field="subject_search" autocomplete="off" placeholder="Search subject code / title..."
                    class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                <div data-role="subject-options"
                    class="hidden absolute z-20 left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-xl"></div>
                <div class="mt-1.5 flex items-center gap-2">
                    <span data-role="subject-code" class="text-xs font-mono text-neutral-500 dark:text-neutral-400">—</span>
                    {{-- Fixes the subject in the master Subjects list (shown once a subject is picked). --}}
                    <button type="button" data-action="edit-subject" title="Edit subject code / title"
                        class="hidden items-center gap-1 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-md transition-colors">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                        Edit
                    </button>
                </div>
            </div>
            <div class="col-span-3 sm:col-span-2">
                <input type="number" data-field="credit" min="0" max="30" step="1" required title="Credit" placeholder="CR"
                    class="w-full px-2 py-2 text-sm text-center bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                <div class="mt-1 text-[10px] text-center text-neutral-400">CR</div>
            </div>
            <div class="col-span-4 sm:col-span-2">
                <input type="number" data-field="score" min="0" max="100" step="0.01" title="Percent grade (empty = IP)" placeholder="IP"
                    class="w-full px-2 py-2 text-sm text-center bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                <div class="mt-1 text-[10px] text-center text-neutral-400">Score %</div>
            </div>
            <div class="col-span-4 sm:col-span-2 flex items-start gap-1.5 pt-1">
                <div class="flex-1 text-center">
                    <div data-role="grade" class="text-sm font-bold text-neutral-900 dark:text-white">IP</div>
                    <div data-role="grade-point" class="text-[10px] text-neutral-400">—</div>
                </div>
                <button type="button" data-action="remove-course" title="Remove"
                    class="p-1 text-neutral-400 hover:text-rose-500 transition-colors">
                    <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </template>

    <script>
        window.TRANSCRIPT_DATA = @json(['subjects' => $subjects]);
        window.TRANSCRIPT_PERMISSIONS = {{ Js::from([
            'edit'         => auth()->user()->can('certificate.edit'),
            'delete'       => auth()->user()->can('certificate.delete'),
            'subject_edit' => auth()->user()->can('subject.view') && auth()->user()->can('subject.edit'),
        ]) }};
    </script>
@endsection

@push('scripts')
    @vite(['resources/js/transcript/index.js'])
@endpush
