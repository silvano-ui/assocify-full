<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}
        
        <div class="flex justify-end mt-4">
            <x-filament::button type="submit">
                Download Report
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
