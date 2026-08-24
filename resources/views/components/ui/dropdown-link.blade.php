@props(['href' => '#'])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'block py-2 px-4 text-sm rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 dark:hover:text-white',
    ]) }}>
    {{ $slot }}
</a>
