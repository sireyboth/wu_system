 @props(['label' => null])

 <td {{ $attributes->merge(['class' => 'px-4 py-3']) }}>
     {{ $label ?? $slot }}
 </td>
