<!-- The modal itself -->
<x-modal name="modal-dialog" :show="false" max-width="lg" >
    <div class="p-6">
        <h3 class="text-lg font-bold text-neutral-900 mb-4 text-center">
            Create New Shift
        </h3>

        <form id="submit-form">
            <!-- your fields -->
        </form>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button" x-data @click="$dispatch('close-modal', 'modal-dialog')"
                class="px-4 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-200 rounded-xl hover:bg-neutral-50">
                Cancel
            </button>
            <button type="submit" form="submit-form"
                class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">
                Save
            </button>
        </div>
    </div>
</x-modal>
