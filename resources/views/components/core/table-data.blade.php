 @props(['label' => null])

 <td {{ $attributes->merge(['class' => 'px-4 py-3 text-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-300 backdrop-blur-md border-b border-neutral-200 dark:border-white/5']) }}>
     {{ $label ?? $slot }}
 </td>
