@props(['label' => null])

<th {{ $attributes->merge(['scope' => 'col', 'class' => 'px-4 py-3']) }}>
    {{ $label ?? $slot }}
</th>
