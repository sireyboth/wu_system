{{--
    Drop this inside a <template x-for="row in rows"> row.
    "resource" builds the Show/Edit URLs: /{resource}/{row.id} and /{resource}/{row.id}/edit.
    Delete calls the dataTable() destroy(row) method (DELETE {endpoint}/{row.id}).
--}}
@props(['resource'])

<x-ui.dropdown>
    <x-slot:trigger>
        <x-ui.icon-button label="Actions">
            <x-ui.icon-svg name="ellipsis-vertical" />
        </x-ui.icon-button>
    </x-slot:trigger>

    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
        <li>
            <x-ui.dropdown-link class="flex items-center gap-3" x-bind:href="`/{{ $resource }}/${row.id}`">
                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-width="2"
                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                    <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span>View</span>
            </x-ui.dropdown-link>
        </li>
        <li>
            <x-ui.dropdown-link class="flex items-center gap-3" x-bind:href="`/{{ $resource }}/${row.id}`">
                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                </svg>
                <span>Edit</span>
            </x-ui.dropdown-link>
    </ul>

    <div class="py-1 text-sm text-gray-700 dark:text-gray-200">
        <x-ui.dropdown-link class="flex items-center gap-3" x-bind:href="`/{{ $resource }}/${row.id}`">
            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
            </svg>
            <span>Remove</span>
        </x-ui.dropdown-link>
    </div>
</x-ui.dropdown>
