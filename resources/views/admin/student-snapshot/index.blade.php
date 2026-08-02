@extends('layouts.dashboard')

@section('title', 'Snapshot')
@section('content')
    <!-- {{-- Page Header --}} -->
    <x-core.page-header text="Student Snapshot" />

    <x-core.table-card endpoint="/student-snapshots" sort="name">

        <x-slot:toolbar>
            <x-ui.primary-button x-on:click="$dispatch('open-modal', 'form')">
                <x-ui.icon-svg class="h-4 w-4" name="plus" />
                <span>Add</span>
            </x-ui.primary-button>
        </x-slot:toolbar>

        <x-slot:head>
            {{-- <x-core.sortable field="name" label="Product name" /> --}}
            <x-core.table-header label="Name" />
            <x-core.table-header label="Sex" />
            <x-core.table-header label="Nationality" />
            <x-core.table-header label="DOB" />
            <x-core.table-header label="Bath" />
            <x-core.table-header label="Campus" />
            <x-core.table-header label="Major" />
            <x-core.table-header label="Group" />
            <x-core.table-header label="Status" />
            <x-core.table-header label="Effective Date" />
            <x-core.table-header label="Actions" />
        </x-slot:head>

        {{-- body data --}}
        <x-core.table-data x-text="row.student.person.full_name" />
        <x-core.table-data x-text="row.student.person.sex?.charAt(0).toUpperCase()" />
        <x-core.table-data x-text="row.student.person.nationality.name" />
        <x-core.table-data x-text="row.student.person.dob" />
        <x-core.table-data x-text="row.batch.name" />
        <x-core.table-data x-text="row.campus.name" />
        <x-core.table-data x-text="row.major.name" />
        <x-core.table-data x-text="row.group.name" />
        <x-core.table-data x-text="row.status.name" />
        <x-core.table-data x-text="row.effective_date" />
        <x-core.table-data class="flex items-center justify-end">
            <x-core.actions resource="student-snapshots" />
        </x-core.table-data>

    </x-core.table-card>
@endsection

{{-- @push('scripts')
    @vite(['resources/js/student/index.js'])
@endpush --}}
