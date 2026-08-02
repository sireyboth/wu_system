@extends('layouts.dashboard')

@section('title', 'Snapshot')
@section('content')
    <!-- {{-- Page Header --}} -->
    <x-core.page-header text="Student Snapshot" />

    <x-core.table-card endpoint="/student-snapshots" sort="name">

        <x-slot:toolbar>
            <x-ui.primary-button x-on:click="$dispatch('open-modal', 'product-form')">
                <x-ui.icon-svg name="plus" />
                Add
            </x-ui.primary-button>
        </x-slot:toolbar>

        <x-slot:head>
            {{-- <x-core.sortable field="name" label="Product name" /> --}}
            <x-core.th>Student Name</x-core.th>
            <x-core.th>Sex</x-core.th>
            <x-core.th>Nationality</x-core.th>
            <x-core.th>Date of Birth</x-core.th>
            <x-core.th>Bath</x-core.th>
            <x-core.th>Campus</x-core.th>
            <x-core.th>Major</x-core.th>
            <x-core.th>Group</x-core.th>
            <x-core.th>Status</x-core.th>
            <x-core.th>Effective Date</x-core.th>
            <x-core.th>Actions</x-core.th>
        </x-slot:head>

        <template x-for="row in rows" :key="row.id">
            <tr class="border-b dark:border-gray-700">
                <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                    x-text="row.student.person.full_name"></th>
                <td class="px-4 py-3" x-text="row.student.person.sex"></td>
                <td class="px-4 py-3 whitespace-normal" x-text="row.student.person.nationality.name"></td>
                <td class="px-4 py-3" x-text="row.student.person.dob"></td>
                <td class="px-4 py-3" x-text="row.batch.name"></td>
                <td class="px-4 py-3" x-text="row.campus.name"></td>
                <td class="px-4 py-3" x-text="row.major.name"></td>
                <td class="px-4 py-3" x-text="row.group.name"></td>
                <td class="px-4 py-3" x-text="row.status.name"></td>
                <td class="px-4 py-3" x-text="row.effective_date"></td>
                <td class="px-4 py-3 flex items-center justify-end">
                    <x-core.actions resource="student-snapshots" />
                </td>
            </tr>
        </template>

    </x-core.table-card>
@endsection

{{-- @push('scripts')
    @vite(['resources/js/student/index.js'])
@endpush --}}
