@props(['showing' => 'Showing', 'of' => 'of', 'back' => 'Back', 'next' => 'Next'])

<nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
    aria-label="Table navigation">
    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
        {{ $showing }}
        <span class="font-semibold text-gray-900 dark:text-white" x-text="meta.from ?? 0"></span>
        -
        <span class="font-semibold text-gray-900 dark:text-white" x-text="meta.to ?? 0"></span>
        {{ $of }}
        <span class="font-semibold text-gray-900 dark:text-white" x-text="meta.total ?? 0"></span>
    </span>

    <ul class="inline-flex items-stretch -space-x-px">
        <li>
            <button type="button" x-on:click="goToPage(meta.current_page - 1)" x-bind:disabled="meta.current_page <= 1"
                class="flex items-center justify-center h-full py-1.5 px-3 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                <span class="sr-only">{{ $back }}</span>
                <x-ui.icon-svg name="chevron-left" class="w-5 h-5" />
            </button>
        </li>

        <template x-for="(page, index) in pages()" :key="index">
            <li>
                <template x-if="page === '...'">
                    <span
                        class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">...</span>
                </template>
                <template x-if="page !== '...'">
                    <button type="button" x-on:click="goToPage(page)" x-text="page"
                        x-bind:class="page === meta.current_page ?
                            'text-primary-600 bg-primary-50 border-primary-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white' :
                            'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'"
                        class="flex items-center justify-center text-sm py-2 px-3 leading-tight border"></button>
                </template>
            </li>
        </template>

        <li>
            <button type="button" x-on:click="goToPage(meta.current_page + 1)"
                x-bind:disabled="meta.current_page >= meta.last_page"
                class="flex items-center justify-center h-full py-1.5 px-3 text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                <span class="sr-only">{{ $next }}</span>
                <x-ui.icon-svg name="chevron-right" class="w-5 h-5" />
            </button>
        </li>
    </ul>
</nav>
