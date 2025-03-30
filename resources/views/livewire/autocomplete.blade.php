<div class="relative">
    <input type="text" wire:model.live.debounce.500ms="query"
        name="{{ $name ?? '' }}"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        placeholder="{{ $label ?? 'Search...' }}"
        @focus="showResults = true"
       >

    @if(!empty($results))
        <ul class="absolute w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded mt-1 max-h-40 overflow-auto">
            @foreach($results as $key => $label)
            <li wire:click="selectItem('{{ $key }}')"
                class="p-2 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-200">
                {{ $label }}
            </li>
            @endforeach
        </ul>
    @endif
</div>
