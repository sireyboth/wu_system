{{-- resources/views/components/table-card.blade.php --}}
@props(['style' => 'danger'])

<x-card :style="$style">
    <x-card.header class="flex flex-col items-stretch gap-y-4">
        {{-- Layer 1: title/description + action button --}}
        <div class="flex items-start justify-between gap-x-4">
            <div class="inline-flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="text-blue-400 font-semibold">Home</a>
                <span>/</span>
                @isset($title)
                    <x-card.title :as-heading="false">{{ $title }}</x-card.title>
                @endisset
            </div>

            <div>
                @isset($action)
                    {{ $action }}
                @endisset
            </div>
        </div>
    </x-card.header>

    {{-- Body: the actual <x-table> goes here --}}
    <x-card.body class="p-0">
        {{-- Layer 2: per-page select + search --}}
        @isset($toolbar)
            <section class="flex items-center justify-between w-full mb-4 px-4 py-2">
                {{ $toolbar }}
            </section>
        @endisset

        <main>{{ $slot }}</main>
    </x-card.body>

    {{-- Footer: pagination (+ future footer content) --}}
    @isset($footer)
        <x-card.footer class="flex items-center justify-between">
            {{ $footer }}
        </x-card.footer>
    @endisset
</x-card>
