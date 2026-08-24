 @props(['route', 'label' => 'Home', 'icon' => 'home'])

 <x-sidebar-link :route="$route">
     <x-slot name="icon">
         <x-ui.icon-svg :name="$icon" />
     </x-slot>
     {{ $label }}
 </x-sidebar-link>
