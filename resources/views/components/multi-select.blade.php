@props([
    'name',
    'id' => null,
    'placeholder' => '-- Select Options --',
    'options' => [], // Format: ['value' => 'Label'] or array of objects
    'selected' => [], // Array of initially selected values: ['associate', 'bachelor']
    'required' => false,
])

@php
    // Ensure selected is always an array
    $selectedValues = is_array($selected) ? $selected : explode(',', $selected);
    $inputContainerId = $id ?? 'multi-select-' . Str::random(8);
@endphp

<!-- Custom Multi-Select Wrapper -->
<div class="relative w-full js-multi-select-wrapper" id="{{ $inputContainerId }}">
    <!-- Hidden input to hold comma-separated selected values -->
    <input type="hidden" name="{{ $name }}" class="js-hidden-input" value="{{ implode(',', $selectedValues) }}"
        {{ $required ? 'required' : '' }}>

    <!-- Trigger Button -->
    <button type="button"
        class="js-dropdown-btn w-full px-4 py-2.5 text-sm text-left bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-xl text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors flex items-center justify-between">
        <span class="js-dropdown-label truncate text-neutral-400" data-placeholder="{{ $placeholder }}">
            {{ $placeholder }}
        </span>
        <!-- Dropdown Arrow Icon -->
        <svg class="js-dropdown-arrow w-4 h-4 text-neutral-500 transition-transform duration-200 shrink-0 ml-2"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Options Menu -->
    <div
        class="js-dropdown-menu hidden absolute z-20 w-full mt-2 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-xl shadow-lg max-h-60 overflow-y-auto p-1.5 space-y-1">
        @foreach ($options as $value => $label)
            @php
                $isChecked = in_array($value, $selectedValues);
                $checkboxId = 'checkbox-' . $name . '-' . $value; // e.g. "checkbox-group_id-1"
            @endphp

            <label for="{{ $checkboxId }}"
                class="flex items-center justify-between px-3 py-2 text-sm text-neutral-900 dark:text-white rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer transition-colors select-none">
                <span class="js-option-text">{{ $label }}</span>
                <input type="checkbox" id="{{ $checkboxId }}" value="{{ $value }}"
                    class="js-option-checkbox h-4 w-4 text-indigo-600 rounded border-neutral-300 focus:ring-indigo-500"
                    {{ $isChecked ? 'checked' : '' }}>
            </label>
        @endforeach
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize all multi-select instances on the page
                document.querySelectorAll('.js-multi-select-wrapper').forEach(wrapper => {
                    const btn = wrapper.querySelector('.js-dropdown-btn');
                    const menu = wrapper.querySelector('.js-dropdown-menu');
                    const label = wrapper.querySelector('.js-dropdown-label');
                    const arrow = wrapper.querySelector('.js-dropdown-arrow');
                    const hiddenInput = wrapper.querySelector('.js-hidden-input');
                    const checkboxes = wrapper.querySelectorAll('.js-option-checkbox');
                    const defaultPlaceholder = label.getAttribute('data-placeholder');

                    // Function to recalculate label and input value
                    const updateSelection = () => {
                        const selectedValues = [];
                        const selectedTexts = [];

                        checkboxes.forEach(cb => {
                            if (cb.checked) {
                                selectedValues.push(cb.value);
                                const optionText = cb.closest('label').querySelector(
                                    '.js-option-text').innerText.trim();
                                selectedTexts.push(optionText);
                            }
                        });

                        hiddenInput.value = selectedValues.join(',');

                        if (selectedTexts.length > 0) {
                            label.innerText = selectedTexts.join(', ');
                            label.classList.remove('text-neutral-400');
                            label.classList.add('text-neutral-900', 'dark:text-white');
                        } else {
                            label.innerText = defaultPlaceholder;
                            label.classList.add('text-neutral-400');
                            label.classList.remove('text-neutral-900', 'dark:text-white');
                        }
                    };

                    // Initial run on page load (for pre-filled edit forms)
                    updateSelection();

                    // Toggle Dropdown
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isOpen = !menu.classList.contains('hidden');

                        // Close other instances if any are open
                        document.querySelectorAll('.js-dropdown-menu').forEach(m => m.classList.add(
                            'hidden'));
                        document.querySelectorAll('.js-dropdown-arrow').forEach(a => a.classList.remove(
                            'rotate-180'));

                        if (!isOpen) {
                            menu.classList.remove('hidden');
                            arrow.classList.add('rotate-180');
                        }
                    });

                    // Update on Checkbox change
                    checkboxes.forEach(cb => {
                        cb.addEventListener('change', updateSelection);
                    });
                });

                // Global click handler to close dropdowns when clicking outside
                document.addEventListener('click', () => {
                    document.querySelectorAll('.js-dropdown-menu').forEach(m => m.classList.add('hidden'));
                    document.querySelectorAll('.js-dropdown-arrow').forEach(a => a.classList.remove(
                        'rotate-180'));
                });
            });
        </script>
    @endpush
@endonce
