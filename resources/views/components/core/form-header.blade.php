 @props(['title' => null, 'modalName' => 'form'])

 {{-- header (fixed) --}}
 <div class="flex items-center mb-2 justify-between py-2 border-b border-gray-200 dark:border-gray-700">
     <h2 class="text-lg font-medium text-gray-900 dark:text-white"
         x-text="{ create: 'Add New {{ $title }}', edit: 'Edit Exist {{ $title }}', view: 'View Details {{ $title }}' }[mode]">
     </h2>
     <button type="button" x-on:click="$dispatch('close-modal', '{{ $modalName }}')"
         class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
         <span class="sr-only">Close</span>
         <x-ui.icon-svg name="close" />
     </button>
 </div>
