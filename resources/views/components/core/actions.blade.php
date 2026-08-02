{{--
    Drop this inside a <template x-for="row in rows"> row.
    "resource" builds the Show/Edit URLs: /{resource}/{row.id} and /{resource}/{row.id}/edit.
    Delete calls the dataTable() destroy(row) method (DELETE {endpoint}/{row.id}).
--}}
@props(['resource'])

<x-ui.dropdown align="right" width="w-44">
    <x-slot:trigger>
        <x-ui.icon-button label="Actions">
            <x-ui.icon-svg name="ellipsis-vertical" class="w-5 h-5" />
        </x-ui.icon-button>
    </x-slot:trigger>

    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
        <li>
            <x-ui.dropdown-link x-bind:href="`/{{ $resource }}/${row.id}`">Show</x-ui.dropdown-link>
        </li>
        <li>
            <x-ui.dropdown-link x-bind:href="`/{{ $resource }}/${row.id}/edit`">Edit</x-ui.dropdown-link>
        </li>
    </ul>
    <div class="py-1">
        <x-ui.dropdown-link href="#" danger x-on:click.prevent="destroy(row)">Delete</x-ui.dropdown-link>
    </div>
</x-ui.dropdown>
