@props(['href' => '#', 'danger' => false])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' =>
            'block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white ' .
            ($danger ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200'),
    ]) }}>
    {{ $slot }}
</a>
