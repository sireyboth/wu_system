{{-- resources/views/partials/pagination-alpine.blade.php --}}
@if ($paginator->hasPages())
    <div class="flex items-center justify-between w-full">
        <div class="text-sm text-gray-500">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </div>
        <div class="flex items-center gap-x-1">
            <button x-on:click="$dispatch('goto-page', { page: '{{ $paginator->previousPageUrl() }}' })"
                :disabled="{{ $paginator->onFirstPage() ? 'true' : 'false' }}"
                class="px-3 py-1 rounded text-sm text-gray-400 hover:bg-gray-800 disabled:opacity-40">
                Prev
            </button>

            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <button x-on:click="$dispatch('goto-page', { page: '{{ $url }}' })"
                            class="px-3 py-1 rounded text-sm {{ $page == $paginator->currentPage() ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800' }}">
                            {{ $page }}
                        </button>
                    @endforeach
                @endif
            @endforeach

            <button x-on:click="$dispatch('goto-page', { page: '{{ $paginator->nextPageUrl() }}' })"
                :disabled="{{ $paginator->hasMorePages() ? 'false' : 'true' }}"
                class="px-3 py-1 rounded text-sm text-gray-400 hover:bg-gray-800 disabled:opacity-40">
                Next
            </button>
        </div>
    </div>
@endif
