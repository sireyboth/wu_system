@props(['name', 'label' => null, 'hint' => null, 'format' => 'Y-m-d', 'placement' => 'bottom-start'])

<div x-data="datepicker('{{ $name }}', '{{ $format }}')" class="relative">
    <label class="block text-sm mb-2 font-medium text-gray-700 dark:text-gray-300 capitalize">
        {{ $label ?? $name }}
    </label>

    <x-ui.dropdown :placement="$placement" match-width>
        {{-- Trigger --}}
        <x-slot:trigger>
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <input type="text" x-model="form.{{ $name }}" x-bind:disabled="mode === 'view'" readonly
                placeholder="{{ $hint ?? ($label ?? 'Select date') }}"
                class="block w-full pl-10 pr-3 py-2 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600
                               text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500
                               disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
        </x-slot:trigger>

        {{-- Calendar Content --}}
        <div class="p-3" @click.stop>
            {{-- Header --}}
            <div class="flex items-center justify-between mb-2">
                <button type="button" @click.stop="prev()"
                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button type="button" @click.stop="switchView()"
                    class="font-semibold text-gray-800 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 px-2 py-1 rounded-lg transition">
                    <template x-if="view === 'days'">
                        <span>
                            <span x-text="monthNames[month]"></span>
                            <span x-text="year"></span>
                        </span>
                    </template>
                    <template x-if="view === 'months'">
                        <span x-text="year"></span>
                    </template>
                    <template x-if="view === 'years'">
                        <span x-text="yearRangeLabel"></span>
                    </template>
                </button>

                <button type="button" @click.stop="next()"
                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            {{-- DAYS VIEW --}}
            <div x-show="view === 'days'">
                <div
                    class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    <template x-for="day in ['Mo','Tu','We','Th','Fr','Sa','Su']">
                        <div x-text="day"></div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-1">
                    <template x-for="i in blanks">
                        <div></div>
                    </template>

                    <template x-for="day in daysInMonth">
                        <button type="button" @mousedown.prevent="selectDate(day)"
                            class="h-9 w-9 rounded-lg text-sm transition hover:bg-blue-50 dark:hover:bg-blue-900/30"
                            :class="{
                                'bg-blue-600 text-white hover:!bg-blue-600': isSelected(day),
                                'ring-1 ring-blue-400 text-blue-600 dark:text-blue-400': isToday(day) && !
                                    isSelected(day),
                                'text-gray-700 dark:text-gray-200': !isSelected(day)
                            }"
                            x-text="day">
                        </button>
                    </template>
                </div>
            </div>

            {{-- MONTHS VIEW --}}
            <div x-show="view === 'months'" class="grid grid-cols-4 gap-2">
                <template x-for="(m, index) in shortMonthNames" :key="index">
                    <button type="button" @mousedown.prevent="selectMonth(index)"
                        class="py-2 rounded-lg text-sm transition hover:bg-blue-50 dark:hover:bg-blue-900/30"
                        :class="{
                            'bg-blue-600 text-white hover:!bg-blue-600': month === index,
                            'text-gray-700 dark:text-gray-200': month !== index
                        }"
                        x-text="m">
                    </button>
                </template>
            </div>

            {{-- YEARS VIEW --}}
            <div x-show="view === 'years'" class="grid grid-cols-4 gap-2">
                <template x-for="y in yearsInRange" :key="y">
                    <button type="button" @mousedown.prevent="selectYear(y)"
                        class="py-2 rounded-lg text-sm transition hover:bg-blue-50 dark:hover:bg-blue-900/30"
                        :class="{
                            'bg-blue-600 text-white hover:!bg-blue-600': year === y,
                            'text-gray-700 dark:text-gray-200': year !== y
                        }"
                        x-text="y">
                    </button>
                </template>
            </div>

            {{-- Footer --}}
            <div x-show="view === 'days'"
                class="flex justify-between p-2 border-t border-gray-200 dark:border-gray-700">
                <button type="button" @mousedown.prevent="clear()"
                    class="text-sm text-gray-500 hover:text-gray-800 dark:hover:text-gray-200">
                    Clear
                </button>
                <button type="button" @mousedown.prevent="selectToday()"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    Today
                </button>
            </div>
        </div>
    </x-ui.dropdown>

    <p class="mt-1 text-sm text-red-600 dark:text-red-400" x-show="errors.{{ $name }}"
        x-text="errors.{{ $name }}?.[0]">
    </p>
</div>
