{{--
    <x-ui.table-toolbar />                              — plain search, no field picker
    <x-ui.table-toolbar :fields="$searchFields" />       — adds the field-picker dropdown
    <x-ui.table-toolbar hint="Search users..." />        — custom placeholder

    Must be rendered inside a listTable()-backed x-data element (i.e. inside
    <x-core.table>) — it reads perPage/searchField/search/loading/meta
    directly from that parent scope via normal Alpine/DOM nesting, so none
    of those are passed as props here, only the Blade-level config below.
--}}
@props([
    'fields' => null,        // array, e.g. ['name' => 'Name', 'email' => 'Email'] — renders the field picker if set
    'hint' => 'Search...',
    'perPageOptions' => [5, 10, 15, 25, 50],
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 w-full border-b border-neutral-200 dark:border-neutral-800">
    <div class="w-full sm:w-28 sm:shrink-0">
        <x-form-select x-model.number="perPage" class="w-full rounded-lg">
            @foreach($perPageOptions as $option)
                <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </x-form-select>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
        @if($fields)
            <div class="w-full sm:w-40 sm:shrink-0">
                <x-form-select x-model="searchField" class="w-full rounded-lg">
                    <option value="">All fields</option>
                    @foreach($fields as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-form-select>
            </div>
        @endif

        <div class="w-full sm:min-w-[16rem] sm:flex-1">
            <x-form-input name="search" type="search" placeholder="{{ $hint }}" x-model="search" class="w-full">
                <x-slot:icon-prefix>
                    <x-form-icon icon="heroicon-o-magnifying-glass" />
                </x-slot:icon-prefix>
                <x-slot:suffix>
                    <span x-text="meta.total"></span>
                </x-slot:suffix>
            </x-form-input>
        </div>
    </div>
</div>
