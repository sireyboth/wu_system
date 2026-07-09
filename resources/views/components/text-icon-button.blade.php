<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all active:scale-95']) }}>
    <x-icon :name="$icon" class="w-6 h-6 mr-1" />
    {{Str::upper($label)  }}
</button>


