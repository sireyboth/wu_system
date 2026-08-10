 @props(['title', 'options' => [], 'size' => 4])

 @if ($options)
     <li class="pt-4 pb-1">
         <span class="px-3 text-xs font-semibold tracking-wider uppercase text-neutral-400 dark:text-neutral-500">
             {{ $title }}
         </span>
     </li>

     @foreach ($options as $option)
         <x-sidebar-link :route="$option['route']">
             <x-slot name="icon">
                 <x-dynamic-component :component="'fas-' . $option['icon']" class="w-{{ $size }} h-{{ $size }}" />
                 {{-- @svg('fas-' . $option['icon'], 'w-4 h-4') --}}
             </x-slot>
             {{ $option['text'] }}
         </x-sidebar-link>
     @endforeach
 @endif
