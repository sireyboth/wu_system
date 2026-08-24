@php
    $modal_name = 'sample-form';
    $endpoint = '/terms';
@endphp


<div x-data="sampleForm({ name: @js($modal_name), endpoint: @js($endpoint) })" x-init="init()">
    <x-core.form-card :name="$modal_name">
        <x-form-section>
            <x-form-section grid-columns="2">
                <x-form-input x-model="form.year" type="number" label="Year" placeholder="Enter the value"
                    @input="updateCode()" x-bind:disabled="mode === 'view'" />

                <x-form-select x-model="form.semester" label="Semester" :options="[1 => 'Semester 1', 2 => 'Semester 2']"
                    x-bind:disabled="mode === 'view'" @change="updateCode()" />
            </x-form-section>

            <x-form-section grid-columns="2">
                <x-ui.datepicker name="start" label="Start Date" hint="Select a date" />
                <x-ui.datepicker name="end" label="End Date" hint="Select a date" />
            </x-form-section>

            <x-form-section grid-columns="2">
                <x-form-input x-model="form.name" label="Academic Year" placeholder="Enter the value"
                    x-bind:disabled="mode === 'view'" />
                <x-form-input x-model="form.code" label="Code" placeholder="Enter the value" readonly
                    x-bind:disabled="mode === 'view'" />
            </x-form-section>

            <x-form-toggle x-model="form.active" label="Status" x-bind:disabled="mode === 'view'" />
            <x-form-textarea x-model="form.remark" label="Remark" placeholder="Enter remark"
                x-bind:disabled="mode === 'view'" />
        </x-form-section>
    </x-core.form-card>
</div>
{{-- 'year',
'semester',
'code',
'name',
'start',
'end',
'active',
'remark', --}}
