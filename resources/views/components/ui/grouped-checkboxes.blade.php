@props([
    'name', // e.g. "shift_ids"
    'options' => [], // static / DB data
    'label' => null,
    'valueKey' => 'id',
    'labelKey' => 'name',
    'inline' => false,
    'alpineOptions' => null, // ← for Alpine API: "shifts"
])

@php
    $inputName = str_ends_with($name, '[]') ? $name : $name . '[]';
    $modelName = str_replace(['[', ']'], '', $name);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @if ($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 capitalize">
            {{ $label }}
        </label>
    @endif

    {{-- ==================== STATIC / DB DATA ==================== --}}
    @if (!$alpineOptions)
        <div class="{{ $inline ? 'flex flex-wrap gap-4' : 'space-y-3' }}">
            @foreach ($options as $option)
                @php
                    if (is_array($option) || is_object($option)) {
                        $optValue = data_get($option, $valueKey);
                        $optLabel = data_get($option, $labelKey);
                    } else {
                        $optValue = $option;
                        $optLabel = $option;
                    }
                @endphp

                <div class="flex items-center">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="{{ $inputName }}" value="{{ $optValue }}"
                            x-model="form.{{ $modelName }}" x-bind:disabled="mode === 'view'"
                            class="w-4 h-4 rounded border-gray-300 bg-white
                                   text-blue-600
                                   checked:bg-blue-600 checked:border-blue-600
                                   dark:bg-gray-700 dark:border-gray-600
                                   dark:checked:bg-blue-600 dark:checked:border-blue-600
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $optLabel }}
                        </span>
                    </label>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ==================== ALPINE / API DATA ==================== --}}
    @if ($alpineOptions)
        <div class="{{ $inline ? 'flex flex-wrap gap-4' : 'space-y-3' }}" x-show="{{ $alpineOptions }}.length">
            <template x-for="item in {{ $alpineOptions }}" :key="item.{{ $valueKey }}">
                <div class="flex items-center">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" :value="item.{{ $valueKey }}" x-model="form.{{ $modelName }}"
                            x-bind:disabled="mode === 'view'"
                            class="w-4 h-4 rounded border-gray-300 bg-white
                                   text-blue-600
                                   checked:bg-blue-600 checked:border-blue-600
                                   dark:bg-gray-700 dark:border-gray-600
                                   dark:checked:bg-blue-600 dark:checked:border-blue-600
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                            x-text="item.{{ $labelKey }}"></span>
                    </label>
                </div>
            </template>
        </div>
    @endif

    {{-- Error --}}
    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-show="errors.{{ $modelName }}"
        x-text="errors.{{ $modelName }}?.[0]">
    </p>
</div>
