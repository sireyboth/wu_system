@props(['hint' => 'Search...'])

<div class="flex items-center justify-between gap-x-4 p-4 w-full border-b border-neutral-200 dark:border-neutral-800">
    <div class="w-40 shrink-0">
        <x-form-select x-model.number="perPage" class="rounded-lg">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </x-form-select>
    </div>

    <div class="flex-1 max-w-sm ml-auto">
        <x-form-input name="search" type="search" placeholder="{{ $hint }}" x-model="search"
            class="rounded-lg">
            <x-slot:icon-prefix>
                <x-form-icon icon="heroicon-o-magnifying-glass" />
            </x-slot:icon-prefix>
        </x-form-input>
    </div>
</div>
