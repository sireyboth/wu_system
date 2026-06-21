@props(['title'])

<div class="mb-6">
  <h1 class="text-2xl font-bold text-neutral-900 dark:text-indigo-600">
    {{ $title }}
  </h1>

  @if($slot->isNotEmpty())
    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
      {{ $slot }}
    </p>
  @endif
</div>
