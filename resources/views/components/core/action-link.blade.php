 @props(['route', 'label' => 'Home', 'icon' => 'home'])

 <x-sidebar-link :route="$route">
     <x-slot name="icon">
         <x-ui.icon-svg :name="$icon" class="w-5 h-5" />
     </x-slot>
     {{ $label }}
 </x-sidebar-link>
