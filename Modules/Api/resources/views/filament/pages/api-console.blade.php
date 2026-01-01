<x-filament-panels::page>
    <x-filament::section>
        <form wire:submit="sendRequest" class="space-y-4">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                    Send Request
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    @if($response)
        <x-filament::section>
            <x-slot name="heading">
                Response
                <span class="ml-2 text-sm font-normal text-gray-500">
                    Status: <span class="font-bold {{ str_starts_with($responseStatus, '2') ? 'text-success-600' : 'text-danger-600' }}">{{ $responseStatus }}</span>
                    | Time: {{ $responseTime }}
                </span>
            </x-slot>
            <pre class="bg-gray-900 text-green-400 p-4 rounded overflow-auto max-h-96 text-sm font-mono">{{ $response }}</pre>
        </x-filament::section>
    @endif
</x-filament-panels::page>
