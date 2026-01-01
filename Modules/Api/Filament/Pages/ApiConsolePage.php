<?php

namespace Modules\Api\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;

class ApiConsolePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-command-line';

    protected string $view = 'api::filament.pages.api-console';

    protected static string | \UnitEnum | null $navigationGroup = 'API';

    protected static ?string $title = 'API Console';

    public ?array $data = [];
    public ?string $response = null;
    public ?string $responseStatus = null;
    public ?string $responseTime = null;

    public function mount(): void
    {
        $this->form->fill([
            'method' => 'GET',
            'url' => url('/api/v1/'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'DELETE' => 'DELETE',
                        'PATCH' => 'PATCH',
                    ])
                    ->required()
                    ->columnSpan(1),
                TextInput::make('url')
                    ->required()
                    ->url()
                    ->columnSpan(3),
                KeyValue::make('headers')
                    ->keyLabel('Header')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->rows(5)
                    ->columnSpanFull(),
            ])
            ->statePath('data')
            ->columns(4);
    }

    public function sendRequest()
    {
        $data = $this->form->getState();
        $start = microtime(true);
        
        try {
            $response = Http::withHeaders($data['headers'])
                ->send($data['method'], $data['url'], [
                    'body' => $data['body'] ?? null,
                ]);

            $this->responseTime = round((microtime(true) - $start) * 1000) . 'ms';
            $this->responseStatus = $response->status() . ' ' . $response->reason();
            $this->response = json_encode($response->json(), JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            $this->responseStatus = 'Error';
            $this->response = $e->getMessage();
        }
    }
}
