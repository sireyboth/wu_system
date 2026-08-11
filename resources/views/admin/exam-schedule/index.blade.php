@extends('layouts.dashboard')
@section('title', 'Exam Schedule')
@section('content')
    <!-- {{-- Page Header --}} -->
    <x-core.page-header welcome="Welcome to" title="Exam Schedule" />

    <x-ui.action-group>
        <x-btn size="sm"> Add Room </x-btn>
        <x-btn size="sm"> Add Room </x-btn>
    </x-ui.action-group>


    <x-core.table endpoint="/users" sort="name">
        <x-slot:header>
            <x-ui.sortable field="name" label="Full Name" />
            <x-table-head>Email Address</x-table-head>
            <x-table-head>Created At</x-table-head>
            <x-table-head>Updated At</x-table-head>
            <x-table-head>Actions</x-table-head>
        </x-slot:header>

        <x-slot:content>
            <x-table-cell x-text="row.name" />
            <x-table-cell x-text="row.email" />
            <x-table-cell x-text="$formatDate(row.created_at, 'datetime')" />
            <x-table-cell x-text="$formatDate(row.updated_at, 'relative')" />
            <x-table-cell>
                <x-btn size="sm"> Add Room </x-btn>
                <x-btn size="sm"> Add Room </x-btn>
            </x-table-cell>
        </x-slot:content>
    </x-core.table>

    @include('admin.exam-schedule._form')
@endsection

@pushOnce('scripts')
    @vite(['resources/js/status/status.js'])
@endPushOnce
