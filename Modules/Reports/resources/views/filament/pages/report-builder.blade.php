<x-filament-panels::page>
    <form wire:submit="generateReport">
        {{ $this->form }}
        
        <div class="flex justify-end mt-4">
            <x-filament::button type="submit">
                Generate Report
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
