@extends('layouts.dashboard')
@section('title', 'Sample')
@section('content')
    <x-page-header title="គម្រូ" />

    <x-data-table-card endpoint="/groups" :columns="[
        ['key' => 'name_kh', 'label' => 'NAME KHMER', 'sortable' => true],
        ['key' => 'name_en', 'label' => 'NAME ENGLISH', 'sortable' => true],
        ['key' => 'shortcut', 'label' => 'SHORTCUT'],
        ['key' => 'remark', 'label' => 'REMARK'],
        ['key' => 'created_at', 'label' => 'CREATE AT', 'sortable' => true],
    ]" search-placeholder="Search Invoice number, type, status..."
        creatable="true">
        <td class="px-4 py-3 font-medium text-indigo-400" x-text="item.name_kh"></td>
        <td class="px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="grid place-items-center w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                </span>
                <div>
                    <div class="text-neutral-100" x-text="item.name_en"></div>
                    <div class="text-xs text-neutral-500" x-text="item.shortcut"></div>
                </div>
            </div>
        </td>
        <td class="px-4 py-3 text-neutral-300" x-text="item.shortcut"></td>
        <td class="px-4 py-3 text-neutral-500" x-text="item.remark || 'No remarks'"></td>
        <td class="px-4 py-3 text-neutral-400" x-text="item.created_at"></td>
    </x-data-table-card>

    @include('sample.form')
@endsection

@push('scripts')
    @vite(['resources/js/sample.js'])
@endpush
