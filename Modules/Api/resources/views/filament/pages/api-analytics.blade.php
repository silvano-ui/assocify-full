<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-filament::section>
            <x-slot name="heading">
                Total Requests (24h)
            </x-slot>
            <div class="text-3xl font-bold">1,234</div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Avg Response Time
            </x-slot>
            <div class="text-3xl font-bold">45ms</div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Error Rate
            </x-slot>
            <div class="text-3xl font-bold text-danger-600">0.5%</div>
        </x-filament::section>
    </div>

    <x-filament::section>
        <x-slot name="heading">
            Traffic Overview
        </x-slot>
        <div class="h-64 bg-gray-50 dark:bg-gray-800 rounded flex items-center justify-center">
            Placeholder for Chart Widget
        </div>
    </x-filament::section>
</x-filament-panels::page>
