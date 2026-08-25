@props([
    'name',
    'title' => null,
    'save' => 'Save',
    'update' => 'Update',
    'close' => 'Close',
    'cancel' => 'Cancel',
    'size' => '2xl',
])

<x-ui.modal-dialog :name="$name" :size="$size">
    <x-form @submit.prevent="submit">
        <x-card class="flex flex-col max-h-[90vh]">
            {{-- header (fixed) --}}
            <x-card.header class="border-b border-gray-400 dark:border-gray-700 shrink-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white"
                        x-text="{ create: 'Add New {{ $title }}', edit: 'Edit Exist {{ $title }}', view: 'View Detail {{ $title }}' }[mode]">
                    </h2>
                    <x-ui.button x-on:click="$dispatch('close-modal', '{{ $name }}')" icon="fas-xmark"
                        variant="ghost" rounded="full" />
                </div>
            </x-card.header>

            {{-- content (dynamic) --}}
            <x-card.body class="flex-1 overflow-y-auto max-h-[70vh]">
                {{ $slot }}
            </x-card.body>

            {{-- footer (fixed) --}}
            <x-card.footer class="justify-end border-t border-gray-400 dark:border-gray-700 shrink-0">
                <div class="mt-2 gap-2">
                    <x-ui.button x-on:click="$dispatch('close-modal', '{{ $name }}')" icon="fas-xmark"
                            variant="secondary">
                            <span x-text="mode === 'view' ? '{{ $close }}' : '{{ $cancel }}'"></span>
                        </x-ui.button>
                        <x-ui.button type="submit" x-show="mode !== 'view' && mode === 'create'" variant="primary"
                            icon="fas-save" x-bind:disabled="loading">
                            {{ $save }}
                        </x-ui.button>
                        <x-ui.button type="submit" x-show="mode !== 'view' && mode === 'edit'" variant="success"
                            icon="fas-redo-alt" x-bind:disabled="loading">
                            {{ $update }}
                        </x-ui.button>
                </div>
            </x-card.footer>
        </x-card>
    </x-form>
</x-ui.modal-dialog>
