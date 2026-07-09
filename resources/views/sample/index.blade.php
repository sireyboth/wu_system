@extends('layouts.dashboard')
@section('title', 'Sample')
@section('content')
    <x-page-header title="គម្រូ" />

    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <x-table-search route="sample.index" input-id="input" form-id="form" />

            <div class="flex items-center gap-2">
                <x-text-icon-button x-data @click="$dispatch('open-modal', 'modal-dialog')" />
            </div>
        </div>

        <x-data-table-card>
            <x-slot name="head">
                <x-sortable-th sortable>#</x-sortable-th>
                <x-sortable-th>Name Khmer</x-sortable-th>
                <x-sortable-th>Name English</x-sortable-th>
                <x-sortable-th>Shortcut</x-sortable-th>
                <x-sortable-th>Remark</x-sortable-th>
                <x-sortable-th>Created At</x-sortable-th>
                <x-sortable-th class="text-right">Actions</x-sortable-th>
            </x-slot>
        </x-data-table-card>
    </div>

    @include('sample.form')
@endsection

@push('scripts')
    @vite(['resources/js/sample.js'])
@endpush
