@props(['name', 'label' => null, 'type' => 'text', 'hint' => null])

<div>
    <label class="block text-sm mb-2 font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $label ?? $name }}</label>
    <input
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 disabled:opacity-60']) }}
        type="{{ $type }}" placeholder="{{ $hint ?? $label }}" x-model="form.{{ $name }}"
        x-bind:disabled="mode === 'view'">
    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-show="errors.{{ $name }}"
        x-text="errors.{{ $name }}?.[0]"></p>
</div>
