@php
    $modal_name = 'sample-form';
    $title = 'Sample Test';
    $endpoint = '/terms';
@endphp

@extends('layouts.dashboard')
@section('title', $title)
@section('content')
    <!-- {{-- Page Header --}} -->
    <x-core.page-header welcome="Welcome to" :title="$title" />


    <x-ui.action-group title="Sample">
        <x-ui.button icon="fas-plus" @click="$dispatch('open-modal', { name: '{{ $modal_name }}', mode: 'create' })">
            Add
        </x-ui.button>
    </x-ui.action-group>


    <x-core.tabledata :endpoint="$endpoint" :searchFields="$searchable">
        <x-slot:header>
            <x-ui.sortable field="display" label="Display Name" />
            <x-ui.sortable field="code" label="Code" />
            <x-table-head>Started</x-table-head>
            <x-table-head>Ended</x-table-head>
            <x-table-head>Group By</x-table-head>
            <x-table-head>Status</x-table-head>
            <x-table-head>Remark</x-table-head>
            <x-table-head>Created</x-table-head>
            <x-table-head>Updated</x-table-head>
            <x-table-head>Actions</x-table-head>
        </x-slot:header>

        <x-slot:content>
            <x-table-cell x-text="item.display" />
            <x-table-cell x-text="item.code" />
            <x-table-cell x-text="item.start" />
            <x-table-cell x-text="item.end" />
            <x-table-cell x-text="item.year" />
            <x-table-cell>
                <x-badge x-text="$getStatus(item.active)" x-show="item.active" style="info" />
                <x-badge x-text="$getStatus(item.active)" x-show="!item.active" style="danger" />
            </x-table-cell>
            <x-table-cell x-text="item.remark" />
            <x-table-cell x-text="$formatDate(item.created_at, 'datetime')" />
            <x-table-cell x-text="$formatDate(item.updated_at, 'relative')" />
            <x-table-cell>
                <x-ui.action-trigger :name="$modal_name" />
            </x-table-cell>
        </x-slot:content>
    </x-core.tabledata>

    @include('admin.sample._form')
@endsection
