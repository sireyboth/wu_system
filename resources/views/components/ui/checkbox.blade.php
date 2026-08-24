@props(['name', 'label' => null])

<div class="flex items-center">
    <label class="inline-flex items-center cursor-pointer select-none">
        <input type="checkbox" {{ $attributes->merge() }} name="{{ $name }}" value="1"
            x-model="form.{{ $name }}" x-bind:disabled="mode === 'view'"
            class="w-4 h-4 rounded border-gray-300 bg-white
                   text-blue-600
                   checked:bg-blue-600 checked:border-blue-600
                   dark:bg-gray-700 dark:border-gray-600
                   dark:checked:bg-blue-600 dark:checked:border-blue-600
                   disabled:opacity-50 disabled:cursor-not-allowed">

        @if ($label || $name)
            <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                {{ $label ?? $name }}
            </span>
        @endif
    </label>

    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-show="errors.{{ $name }}"
        x-text="errors.{{ $name }}?.[0]">
    </p>
</div>
