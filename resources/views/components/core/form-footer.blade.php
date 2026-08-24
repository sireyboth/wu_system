@props(['saveLabel' => 'Save', 'cancelLabel' => 'Cancel', 'closeLabel' => 'Close', 'modalName' => 'form'])

<div class="mt-4 py-2 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
    <x-ui.secondary-button class="gap-1" x-on:click="$dispatch('close-modal', '{{ $modalName }}')">
        <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span x-text="mode === 'view' ? '{{ $closeLabel }}' : '{{ $cancelLabel }}'"></span>
    </x-ui.secondary-button>

    <x-ui.primary-button x-show="mode !== 'view'" x-on:click="submit()" x-bind:disabled="loading" class="gap-2">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                d="M4 5a1 1 0 0 1 1-1h11.586a1 1 0 0 1 .707.293l2.414 2.414a1 1 0 0 1 .293.707V19a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z" />
            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                d="M8 4h8v4H8V4Zm7 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <span>{{ $saveLabel }}</span>
    </x-ui.primary-button>
</div>
