<x-filament-panels::page>
    <div class="flex gap-4 border-b border-gray-200 dark:border-gray-700">
        @foreach($this->getTabs() as $key => $label)
            <button 
                wire:click="$set('activeTab', '{{ $key }}')"
                class="px-4 py-2 {{ $activeTab === $key ? 'border-b-2 border-primary-500 text-primary-600 font-bold' : 'text-gray-500' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{ $this->table }}
</x-filament-panels::page>
