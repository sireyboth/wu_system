{{--
    Usage:
    <x-table.filter-dropdown
        title="Choose brand"
        filter-key="brand"
        :options="[
            ['value' => 'apple', 'label' => 'Apple', 'count' => 56],
            ['value' => 'microsoft', 'label' => 'Microsoft', 'count' => 16],
        ]"
    />
--}}
@props(['title' => 'Filter', 'filterKey', 'options' => []])

<x-ui.dropdown align="right" width="w-48">
    <x-slot:trigger>
        <x-ui.secondary-button>
            <x-ui.icon-svg name="filter" class="h-4 w-4 mr-2 text-gray-400" />
            {{ $title }}
            <x-ui.icon-svg name="chevron-down" class="ml-1.5 w-5 h-5" />
        </x-ui.secondary-button>
    </x-slot:trigger>

    <div class="p-3" x-on:click.stop>
        <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</h6>
        <ul class="space-y-2 text-sm">
            @foreach ($options as $option)
                <li class="flex items-center">
                    <input id="filter-{{ $filterKey }}-{{ $option['value'] }}" type="checkbox"
                        x-on:change="toggleFilter('{{ $filterKey }}', '{{ $option['value'] }}')"
                        class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <label for="filter-{{ $filterKey }}-{{ $option['value'] }}"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $option['label'] }} ({{ $option['count'] }})
                    </label>
                </li>
            @endforeach
        </ul>
    </div>
</x-ui.dropdown>
