{{-- Assumes this renders inside a parent x-data="dataTable(...)" scope,
     so it reads page / totalPages / total directly rather than via props. --}}

<div class="flex items-center justify-between text-sm text-neutral-400">
    <span>
        Page <span x-text="page"></span> of <span x-text="totalPages || 1"></span>
        <span x-show="total !== undefined">&mdash; <span x-text="total"></span> total</span>
    </span>

    <div class="flex items-center gap-2">
        <button
            type="button"
            x-on:click="page = Math.max(1, page - 1)"
            x-bind:disabled="page <= 1"
            class="px-3 py-1.5 rounded-lg border border-neutral-800 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-neutral-800"
        >
            Prev
        </button>
        <button
            type="button"
            x-on:click="page = Math.min(totalPages, page + 1)"
            x-bind:disabled="page >= totalPages"
            class="px-3 py-1.5 rounded-lg border border-neutral-800 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-neutral-800"
        >
            Next
        </button>
    </div>
</div>
