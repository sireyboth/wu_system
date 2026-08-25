@props(['route', 'icon' => null, 'active' => null])

@php
    use Illuminate\Support\Facades\Route as RouteFacade;

    $href = $route && RouteFacade::has($route) ? route($route) : '#';

    // Defaults to an exact match on $route. Pass `active` explicitly
    // (comma-separated or array) for links that should also stay active
    // on sibling CRUD routes, e.g. active="student.index,student.create,student.edit"
    $activePatterns = $active ? (is_array($active) ? $active : explode(',', $active)) : [$route];

    $isActive = collect($activePatterns)->filter()->contains(fn($pattern) => request()->routeIs(trim($pattern)));

    $activeClasses = 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-500/10';
    $inactiveClasses = 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-white/5
hover:text-neutral-900 dark:hover:text-white';
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
