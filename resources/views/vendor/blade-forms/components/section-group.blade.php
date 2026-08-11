<div {{ $attributes->class([
    'flex flex-col gap-y-4',
    '[&_section:not(:first-child)]:pt-4 [&_section:not(:first-child)]:border-t [&_section]:border-[var(--border)]',
]) }}>
    {{ $slot }}
</div>
