@props(['showing' => 'Showing', 'to' => '-', 'of' => 'of', 'results' => 'results'])

<div x-show="!loading && meta.last_page > 1" style="display: none;"
    class="flex flex-col sm:flex-row items-center justify-between gap-y-3 px-4 py-3 border-t border-neutral-200 dark:border-white/5">
    <p class="text-sm text-neutral-500 dark:text-neutral-400">
        {{ $showing }} <span x-text="meta.from">
        </span>{{ $to }}<span x-text="meta.to"></span>
        {{ $of }} <span x-text="meta.total"></span>
    </p>

    <div class="flex items-center gap-x-1">
        {{-- meta.links already contains Prev, every numbered page (or "..." gaps), and Next —
             Laravel computed the windowing server-side, so this is just a render loop.
             One <button> per iteration — Alpine's x-for template needs a single root child. --}}
        <template x-for="(link, i) in meta.links" :key="i">
            <button type="button" @click="goToPage(link)" :disabled="!link.url" x-html="link.label"
                class="px-3 py-1 text-sm transition-colors min-w-[2rem] rounded-full"
                :class="link.active ?
                    'bg-indigo-600 text-white' :
                    (!link.url ? 'text-neutral-400 cursor-not-allowed rounded-md' :
                        'text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-md')">
            </button>
        </template>
    </div>
</div>
