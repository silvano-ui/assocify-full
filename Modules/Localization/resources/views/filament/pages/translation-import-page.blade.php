<x-filament-panels::page>
    <x-filament-panels::form wire:submit="import">
        {{ $this->form }}

        <div class="flex justify-end gap-3 mt-4">
            <x-filament::button type="submit">
                Import Translations
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
