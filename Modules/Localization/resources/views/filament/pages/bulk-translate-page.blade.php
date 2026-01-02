<x-filament-panels::page>
    <x-filament-panels::form wire:submit="translate">
        {{ $this->form }}

        <div class="flex justify-end gap-3 mt-4">
            <x-filament::button type="submit">
                Start Translation
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
