@extends('layouts.dashboard')
@section('title', 'Roles & Permissions')
@section('content')

    <x-core.page-header title="តួនាទី និងសិទ្ធិចូលប្រើ (Roles & Permissions)"
        subtitle="គ្រប់គ្រងអ្វីដែលបុគ្គលិកនីមួយៗអាចមើល ឬកែប្រែបាន (Control what each staff member can see and edit)" />

    <div class="space-y-8">
        {{-- Roles --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-neutral-800 dark:text-white">តួនាទី (Roles)</h3>
                <button type="button" onclick="RolesModule.openCreate()"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    បង្កើតតួនាទីថ្មី (Create Role)
                </button>
            </div>

            <div id="roles-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
        </div>

        {{-- Users --}}
        <div>
            <h3 class="text-sm font-bold text-neutral-800 dark:text-white mb-4">បុគ្គលិក (Staff Accounts)</h3>
            <div
                class="relative overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
                <div id="loading-overlay"
                    class="hidden absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-neutral-900/50 backdrop-blur-[2px]">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400 border-collapse">
                        <thead class="text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300">
                            <tr>
                                <th class="px-6 py-3">Name</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3">Role</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body" class="divide-y divide-neutral-100 dark:divide-white/5"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-ui.modal id="roleModal" title="បង្កើតតួនាទីថ្មី (Create Role)" formId="roleForm" maxWidth="max-w-2xl">
        <input type="hidden" name="id">
        <div>
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">
                ឈ្មោះតួនាទី (Role Name)</label>
            <input required type="text" name="name" placeholder="e.g., Exam Officer"
                class="w-full px-4 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-2">
                សិទ្ធិចូលប្រើម៉ូឌុល (Module Permissions)</label>
            <div id="permission-grid" class="space-y-2 border border-neutral-200 dark:border-white/10 rounded-xl p-3 max-h-[45vh] overflow-y-auto"></div>
        </div>

        <x-slot:footer>
            <button type="button" onclick="AppModal.toggle(false)"
                class="px-5 py-2.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5 rounded-xl transition-all">
                បោះបង់ (Cancel)
            </button>
            <button type="submit" form="roleForm" id="roleSubmitBtn"
                class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 active:scale-95 rounded-xl transition-all">
                រក្សាទុក (Save Role)
            </button>
        </x-slot:footer>
    </x-ui.modal>

@endsection

@push('scripts')
    @vite(['resources/js/role/index.js'])
@endpush
