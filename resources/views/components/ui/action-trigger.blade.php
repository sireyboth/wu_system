@props(['name'])

<x-ui.dropdown>
    <x-slot:action>
        <x-ui.button icon="fas-ellipsis" variant="ghost" rounded="full" />
    </x-slot:action>

    <x-slot:content>
        <ul class="py-1 text-sm text-gray-600 dark:text-gray-200">
            <li>
                <x-ui.dropdown-link class="flex items-center gap-3"
                    @click="$dispatch('open-modal', { mode: 'view', name: '{{ $name }}', id: item.id })">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-width="2"
                            d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                        <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span class="font-medium">View</span>
                </x-ui.dropdown-link>
            </li>
            <li>
                <x-ui.dropdown-link class="flex items-center gap-3"
                    @click="$dispatch('open-modal', { mode: 'edit', name: '{{ $name }}', id: item.id })">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                    </svg>
                    <span class="font-medium">Edit</span>
                </x-ui.dropdown-link>
            </li>
        </ul>

        <div class="py-1 text-sm border-t dark:border-t-gray-600 text-gray-600 dark:text-gray-200">
            <x-ui.dropdown-link class="flex items-center gap-3"
                @click="$dispatch('table-action', { id: item.id, action: 'trash' })">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                </svg>
                <span class="font-medium">Trash</span>
            </x-ui.dropdown-link>
        </div>
    </x-slot:content>
</x-ui.dropdown>
