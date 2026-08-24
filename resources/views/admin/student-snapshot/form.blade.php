@php
    $endpoint = '/student-snapshots';
    $modal_name = 'form';
@endphp

<x-core.modal-dialog endpoint="{{ $endpoint }}" name="{{ $modal_name }}">
    <div x-data="resourceForm({
        name: '{{ $modal_name }}',
        endpoint: '{{ $endpoint }}',
        defaults: {
            is_active: false,
            start_date: '',
            shift_ids: [],
            user_ids: []
        }
    })">
        {{-- header (fixed) --}}
        <x-core.form-header modal-name="{{ $modal_name }}" />

        {{-- body (scrolls, fills remaining space) --}}
        <div class="flex-1 overflow-y-auto py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

            <x-ui.datepicker format="d/m/Y" name="start_date" label="Start Date" hint="Select start date" />

            <x-ui.input-group label="Name" name="name" />

            <x-ui.toggle name="is_active" label="Active" />

            <x-ui.checkbox name="remember" label="Remember me" />

            <x-ui.checkbox-group name="shift_ids" label="Select Shifts" :options="[
                ['id' => 1, 'name' => 'Morning'],
                ['id' => 2, 'name' => 'Afternoon'],
                ['id' => 3, 'name' => 'Night'],
            ]" inline />

            <x-ui.multi-select name="user_ids" :options="[
                ['id'=> 1, 'name'=> 'LANN Phorlly'],
                ['id'=> 2, 'name'=> 'THAI Ngounleng'],
                ['id'=> 3, 'name'=> 'SEAM Saron'],
            ]" hint="Dropdown search" />

            <x-ui.input-group label="Price" name="price" type="number" />
        </div>

        {{-- footer (fixed) --}}
        <x-core.form-footer modal-name="{{ $modal_name }}" />
    </div>
</x-core.modal-dialog>
