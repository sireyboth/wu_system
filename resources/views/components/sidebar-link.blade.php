@props(['route', 'icon' => null, 'exact' => false])

@php
    use Illuminate\Support\Facades\Route as RouteFacade;
    use Illuminate\Support\Str;

    $href = $route && RouteFacade::has($route) ? route($route) : '#';

    if ($exact) {
        $isActive = $route ? request()->routeIs($route) : false;
    } else {
        $base = $route ? Str::before($route, '.') : '';
        $isActive = $base ? request()->routeIs($base . '.*') : false;
    }

    $activeClasses = 'text-indigo-600 dark:text-indigo-400
                      bg-indigo-50 dark:bg-indigo-500/10
                      border border-indigo-200 dark:border-indigo-500/20
                      shadow-sm';

    $inactiveClasses = 'text-neutral-600 dark:text-neutral-400
                        hover:bg-neutral-100 dark:hover:bg-white/5
                        hover:text-neutral-900 dark:hover:text-white
                        border border-transparent';
@endphp

<li>
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' =>
                'flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group ' .
                ($isActive ? $activeClasses : $inactiveClasses),
        ]) }}>
        @if ($icon)
            <div class="transition-transform duration-200 group-hover:scale-110">
                {{ $icon }}
            </div>
        @endif

        <span class="ms-3 font-semibold">{{ $slot }}</span>
    </a>
</li>
