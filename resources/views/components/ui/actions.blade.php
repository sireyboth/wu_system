@props(['resource'])
<div class="flex items-center gap-x-3" x-data="{ confirming: false, deleting: false }">
    <template x-if="!confirming">
        <div class="flex items-center gap-x-3">
            <a :href="`/{{ $resource }}/${row.id}/edit`" class="text-indigo-500 hover:underline text-sm">Edit</a>
            <button type="button" @click="confirming = true" class="text-red-500 hover:underline text-sm">Delete</button>
        </div>
    </template>

    <template x-if="confirming">
        <div class="flex items-center gap-x-2">
            <button type="button" class="text-xs text-neutral-400 hover:text-neutral-600" @click="confirming = false"
                :disabled="deleting">Cancel</button>
            <button type="button" class="text-xs text-red-500 font-medium disabled:opacity-50" :disabled="deleting"
                @click="
                    deleting = true;
                    $api.delete(`/{{ $resource }}/${row.id}`)
                        .then(() => refresh())
                        .finally(() => { deleting = false; confirming = false })
                ">
                <span x-text="deleting ? 'Deleting…' : 'Confirm'"></span>
            </button>
        </div>
    </template>
</div>
