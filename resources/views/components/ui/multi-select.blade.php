@props([
    'name',
    'options' => [],
    'choices' => null,
    'valueKey' => 'id',
    'labelKey' => 'name',
    'hint' => 'Select...',
    'searchHint' => 'Search...',
    'apiUrl' => null,
    'placement' => 'bottom-start',
    'noData' => 'No results found',
])

@php
    $model_name = str_replace(['[', ']'], '', $name);
@endphp

<div x-data="multiSelect({
    options: {{ $choices ? '[]' : Js::from($options) }},
    valueKey: '{{ $valueKey }}',
    labelKey: '{{ $labelKey }}',
    modelName: '{{ $model_name }}',
    placeholder: '{{ $hint }}'
})"
    @if ($apiUrl) x-init="load('{{ $apiUrl }}')"
    @elseif($choices)
        x-init="options = {{ $choices }}" @endif>

    <x-ui.dropdown :placement="$placement" match-width>
        {{-- Trigger --}}
        <x-slot:trigger>
            <button type="button"
                class="inline-flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium
                       text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                <span x-text="displayText"></span>
                <svg class="w-4 h-4 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </x-slot:trigger>

        {{-- Content - colors from picture 1 --}}
        <div @click.stop>
            {{-- Search --}}
            <div class="p-3 border-b border-gray-700">
                <input type="text" x-model="search" placeholder="{{ $searchHint }}"
                    class="w-full px-3 py-2 text-sm bg-gray-900 border border-gray-600 rounded-lg
                           text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
            </div>

            {{-- Checkbox List --}}
            <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                <template x-for="item in filtered" :key="item[valueKey] || item">
                    <label
                        class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer
                                  hover:bg-gray-700 transition select-none">
                        <input type="checkbox" :value="item[valueKey] || item" x-model="form[modelName]"
                            class="w-4 h-4 rounded border-gray-500 bg-gray-700
                                   text-blue-600 checked:bg-blue-600 checked:border-blue-600 focus:ring-0">
                        <span class="text-sm text-gray-200" x-text="item[labelKey] || item"></span>
                    </label>
                </template>

                <div x-show="filtered.length === 0" class="px-3 py-4 text-sm text-gray-400 text-center">
                    {{ $noData }}
                </div>
            </div>

            {{-- Clear all --}}
            <div class="p-3 border-t border-gray-700">
                <button type="button" @click="clear()" :disabled="selectedCount === 0"
                    :class="selectedCount === 0 ?
                        'bg-gray-600 cursor-not-allowed opacity-50' :
                        'bg-red-600 hover:bg-red-700'"
                    class="w-full py-2 text-sm font-medium text-white rounded-lg transition">
                    Clear all
                </button>
            </div>
        </div>
    </x-ui.dropdown>

    {{-- Error --}}
    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-show="errors.{{ $model_name }}"
        x-text="errors.{{ $model_name }}?.[0]">
    </p>
</div>
