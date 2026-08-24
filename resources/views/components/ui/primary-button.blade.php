{{-- Skip this file if you already have a PrimaryButton component — same markup/classes as the original. --}}
@props(['type' => 'button'])

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95',
    ]) }}>
    {{ $slot }}
</button>
