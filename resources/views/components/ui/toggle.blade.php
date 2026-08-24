@props(['name', 'label' => null])

<div class="flex items-center h-full">
    <label class="inline-flex items-center cursor-pointer select-none">
        <input type="checkbox" {{ $attributes->merge() }} name="{{ $name }}" value="1"
            x-model="form.{{ $name }}" x-bind:disabled="mode === 'view'" class="sr-only peer">

        <div
            class="relative w-11 h-6
                   bg-gray-200 dark:bg-gray-600
                   peer-focus:outline-none
                   rounded-full
                   peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                   peer-checked:after:border-white
                   after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                   after:bg-white after:border after:border-gray-300
                   after:rounded-full after:h-5 after:w-5 after:transition-all
                   peer-checked:bg-blue-600
                   peer-disabled:opacity-50 peer-disabled:cursor-not-allowed
                   transition-colors duration-200">
        </div>

        @if ($label || $name)
            <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                {{ $label ?? $name }}
            </span>
        @endif
    </label>

    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-show="errors.{{ $name }}"
        x-text="errors.{{ $name }}?.[0]">
    </p>
</div>
