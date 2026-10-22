@extends('layouts.dashboard')
@section('title', 'Invigilators')
@section('content')

    <x-core.page-header title="អ្នកឃ្លាំមើលការប្រឡង (Invigilators)"
        subtitle="គ្រប់គ្រងព័ត៌មាន ប្រវត្តិ និងកាត QR (Manage profiles, history and QR cards)" />

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
                <input id="invigilatorSearchInput" type="text"
                    class="block w-full p-2.5 ps-10 text-sm text-neutral-900 border border-neutral-200 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-neutral-900 dark:border-white/10 dark:placeholder-neutral-400 dark:text-white transition-all"
                    placeholder="Search by name, ID or batch..." autocomplete="off" />
            </div>

            @can('invigilator.create')
                <button type="button" id="invigilatorAddBtn"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    បន្ថែមអ្នកឃ្លាំមើល (Add Invigilator)
                </button>
            @endcan
        </div>

        <x-ui.data-table :headers="[
            'N.O',
            'ID',
            'Name',
            'Batch',
            'History',
            ['label' => 'Actions', 'align' => 'right'],
        ]" body-id="invigilator-table-body" />
    </div>

    {{-- Create / Edit --}}
    <x-ui.modal id="invigilatorModal" card-id="invigilatorModalCard" title-id="invigilatorModalTitle"
        title="បន្ថែមអ្នកឃ្លាំមើល (Add Invigilator)" form-id="invigilatorForm"
        close-fn="InvigilatorModal" max-width="max-w-2xl">

        {{-- Photo --}}
        <div class="flex items-center gap-4">
            <div class="relative w-24 h-24 shrink-0 rounded-2xl overflow-hidden bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10">
                <img id="invigilatorPhotoPreview" alt="Photo" class="hidden w-full h-full object-cover">
                <svg id="invigilatorPhotoPlaceholder" class="absolute inset-0 m-auto w-10 h-10 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">រូបថត (Photo)</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="invigilatorPhotoPickBtn"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors">
                        ជ្រើសរើសរូប (Choose Photo)
                    </button>
                    <button type="button" id="invigilatorPhotoRemoveBtn"
                        class="hidden px-3 py-1.5 text-xs font-semibold text-rose-500 hover:text-rose-600">
                        លុបរូប (Remove)
                    </button>
                </div>
                <p class="text-[11px] text-neutral-400">JPG / PNG / WEBP, up to 5 MB</p>
                <input type="file" id="invigilatorPhotoInput" accept="image/jpeg,image/png,image/webp" class="hidden">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះខ្មែរ (Name KH) <span class="text-rose-500">*</span></label>
                <input type="text" name="name_kh" required maxlength="100" autocomplete="off" placeholder="ឧ. សុខ ម៉ានី"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ឈ្មោះអង់គ្លេស (Name EN) <span class="text-rose-500">*</span></label>
                <input type="text" name="name_en" required maxlength="100" autocomplete="off" placeholder="e.g. SOK Many"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">អត្តលេខ (Student ID) <span class="text-rose-500">*</span></label>
                <input type="text" name="code" required maxlength="50" autocomplete="off" placeholder="e.g. INV-001"
                    class="w-full px-4 py-2.5 text-sm font-mono bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ជំនាន់ (Batch)</label>
                <input type="text" name="batch" maxlength="100" autocomplete="off" placeholder="e.g. B23"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">ដេប៉ាតឺម៉ង់ (Department)</label>
                <input type="text" name="department" maxlength="100" autocomplete="off" placeholder="e.g. Management"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">បន្ទប់ (Room)</label>
                <input type="text" name="room" maxlength="50" autocomplete="off" placeholder="e.g. Special / B-204"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white dark:placeholder-neutral-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">សុពលភាពដល់ (Valid To)</label>
                <input type="date" name="valid_until"
                    class="w-full sm:w-1/2 px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                <p class="mt-1 text-[11px] text-neutral-400">Leave empty for no end date. After this date the card shows EXPIRED instead of ON DUTY.</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">សម្គាល់ (Remark)</label>
                <textarea name="remark" rows="2" maxlength="500"
                    class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none resize-none"></textarea>
            </div>
        </div>

        {{-- History (repeatable) --}}
        <div class="pt-4 border-t border-neutral-100 dark:border-white/5">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-neutral-900 dark:text-white">ប្រវត្តិ (History)</h4>
                <button type="button" id="invigilatorAddHistoryBtn"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                    បន្ថែមប្រវត្តិ (Add History)
                </button>
            </div>
            <div id="invigilatorHistoryList" class="space-y-3"></div>
            <p id="invigilatorHistoryEmpty" class="text-xs text-center text-neutral-400 py-4 border border-dashed border-neutral-200 dark:border-white/10 rounded-xl">
                មិនទាន់មានប្រវត្តិ (No history yet)
            </p>
        </div>

        <x-slot:footer>
            <button type="button" onclick="InvigilatorModal.toggle(false)"
                class="px-4 py-2.5 text-sm font-semibold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-colors">
                បោះបង់ (Cancel)
            </button>
            <button type="submit" form="invigilatorForm"
                class="px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                រក្សាទុក (Save)
            </button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- One history row; cloned by "Add History". --}}
    <template id="invigilatorHistoryTemplate">
        <div class="history-row relative p-4 bg-neutral-50 dark:bg-white/5 border border-neutral-200 dark:border-white/10 rounded-xl">
            <input type="hidden" data-field="id">
            <button type="button" data-action="remove-history" title="Remove this history row"
                class="absolute top-2 right-2 p-1 text-neutral-400 hover:text-rose-500 transition-colors">
                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-6">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1">ការពិពណ៌នា (Description) <span class="text-rose-500">*</span></label>
                    <textarea data-field="description" rows="2" required maxlength="1000"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1">កាលបរិច្ឆេទ (Date)</label>
                    <input type="date" data-field="date"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1">ការវាយតម្លៃ (Rating)</label>
                    <input type="hidden" data-field="rating">
                    <div class="star-picker flex items-center gap-0.5 h-[38px]">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" data-star="{{ $i }}" title="{{ $i }} / 5"
                                class="text-2xl leading-none text-neutral-300 dark:text-neutral-600 hover:scale-110 transition-transform">★</button>
                        @endfor
                        <button type="button" data-star="0" title="Clear rating"
                            class="ms-2 text-[11px] font-semibold text-neutral-400 hover:text-rose-500">Clear</button>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1">សម្គាល់ (Remark)</label>
                    <input type="text" data-field="remark" maxlength="500"
                        class="w-full px-3 py-2 text-sm bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
            </div>
        </div>
    </template>

    {{-- QR card --}}
    <x-ui.modal id="invigilatorQrModal" card-id="invigilatorQrModalCard" title-id="invigilatorQrModalTitle"
        title="កាត QR (QR Card)" close-fn="InvigilatorQrModal" max-width="max-w-sm">
        <div id="invigilatorQrCard" class="flex flex-col items-center text-center gap-3">
            <img id="invigilatorQrPhoto" alt="Photo" class="hidden w-20 h-20 rounded-2xl object-cover border border-neutral-200 dark:border-white/10">
            <img id="invigilatorQrImage" alt="QR code" class="w-56 h-56 rounded-xl border border-neutral-200 dark:border-white/10 bg-white p-2">
            <div>
                <div id="invigilatorQrNameKh" class="text-lg font-bold text-neutral-900 dark:text-white"></div>
                <div id="invigilatorQrNameEn" class="text-sm text-neutral-600 dark:text-neutral-300"></div>
                <div id="invigilatorQrCode" class="mt-1 text-xs font-mono text-neutral-400"></div>
            </div>
            <a id="invigilatorQrLink" href="#" target="_blank" rel="noopener"
                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline break-all">Open public page</a>
        </div>

        <x-slot:footer>
            <button type="button" id="invigilatorQrDownloadBtn"
                class="px-4 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-200 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl hover:bg-neutral-50 dark:hover:bg-white/5 transition-colors">
                ទាញយក (Download PNG)
            </button>
            <button type="button" id="invigilatorQrPrintBtn"
                class="px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                បោះពុម្ព (Print Card)
            </button>
        </x-slot:footer>
    </x-ui.modal>

    <script>
        window.INVIGILATOR_PERMISSIONS = @json([
            'edit'   => auth()->user()->can('invigilator.edit'),
            'delete' => auth()->user()->can('invigilator.delete'),
        ]);
    </script>
@endsection

@push('scripts')
    @vite(['resources/js/invigilator/index.js'])
@endpush
