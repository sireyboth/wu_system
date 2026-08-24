@props(['label' => null])

<button type="button" @if ($label) aria-label="{{ $label }}" @endif
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100',
    ]) }}>
    {{ $slot }}
</button>
