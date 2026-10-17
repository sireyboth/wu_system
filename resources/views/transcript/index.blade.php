@extends('layouts.dashboard')
@section('title', 'Transcripts')
@section('content')

    @php
        $input = 'w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none';
        $label = 'block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5';
        $section = 'flex items-center gap-2 text-sm font-bold text-neutral-900 dark:text-white';
        $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
    @endphp

    <x-core.page-header title="ព្រឹត្តិប័ត្រពិន្ទុ (Transcript)"
        subtitle="បញ្ចូលពិន្ទុ គណនា GPA និងបោះពុម្ពលើក្រដាសក្បាល (Enter scores, auto GPA, print on letterhead)" />

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

            @can('certificate.create')
                <button type="button" id="transcriptAddBtn"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    បង្កើតព្រឹត្តិប័ត្រ (New Transcript)
                </button>
            @endcan
        </div>

        <x-ui.data-table :headers="[
            'N.O',
            'N° Issue',
            'Student',
            'Major',
            'Credits / GPA',
            'Entrance',
            ['label' => 'Actions', 'align' => 'right'],
        ]" body-id="transcript-table-body" />
    </div>

    {{-- Create / Edit --}}
    <x-ui.modal id="transcriptModal" card-id="transcriptModalCard" title-id="transcriptModalTitle"
        title="បង្កើតព្រឹត្តិប័ត្រ (New Transcript)" form-id="transcriptForm"
        close-fn="TranscriptModal" max-width="max-w-6xl" max-height="h-[calc(100vh-4rem)]">

        {{-- 1. Student --}}
        <section class="space-y-4">
            <h4 class="{{ $section }}">
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white">1</span>
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
                    <input type="text" name="pob" maxlength="150" autocomplete="off" placeholder="e.g. Phnom Penh" class="{{ $input }}">
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
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white">2</span>
                កម្មវិធីសិក្សា (Program)
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
            </div>
        </section>

        {{-- 3. Courses --}}
        <section class="space-y-4 pt-5 border-t border-neutral-100 dark:border-white/5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <h4 class="{{ $section }}">
                    <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white">3</span>
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
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white">4</span>
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
            </div>
        </section>

        {{-- 5. Entrance exam + note --}}
        <section class="space-y-4 pt-5 border-t border-neutral-100 dark:border-white/5">
            <h4 class="{{ $section }}">
                <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-indigo-600 text-white">5</span>
                ប្រឡងចូល និងកំណត់សម្គាល់ (Entrance Exam &amp; Note)
            </h4>
            <label class="flex items-start gap-3 p-4 rounded-2xl border border-neutral-200 dark:border-white/10 cursor-pointer">
                <input type="checkbox" name="entrance_exam" value="1" class="mt-0.5 w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500">
                <span>
                    <span class="block text-sm font-bold text-neutral-900 dark:text-white">បានជាប់ប្រឡងចូល (Entrance exam passed)</span>
                    <span class="block text-xs text-neutral-500">If not passed, the transcript can be saved but <b>cannot be printed</b>.</span>
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

    {{-- One course row; cloned by "Add Subject". --}}
    <template id="transcriptCourseTemplate">
        <div class="course-row relative grid grid-cols-12 gap-2 items-start p-2.5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl">
            <div class="col-span-12 sm:col-span-6 relative">
                <input type="hidden" data-field="subject_id">
                <input type="text" data-field="subject_search" autocomplete="off" placeholder="Search subject code / title..."
                    class="w-full px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                <div data-role="subject-options"
                    class="hidden absolute z-20 left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-xl"></div>
                <div data-role="subject-code" class="mt-1 text-[11px] font-mono text-neutral-400">—</div>
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
        window.TRANSCRIPT_PERMISSIONS = @json([
            'edit'   => auth()->user()->can('certificate.edit'),
            'delete' => auth()->user()->can('certificate.delete'),
        ]);
    </script>
@endsection

@push('scripts')
    @vite(['resources/js/transcript/index.js'])
@endpush
