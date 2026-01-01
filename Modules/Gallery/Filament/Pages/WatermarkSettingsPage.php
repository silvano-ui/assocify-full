<?php

namespace Modules\Gallery\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Modules\Gallery\Entities\WatermarkSetting;

class WatermarkSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';
    protected static string | \UnitEnum | null $navigationGroup = 'Gallery';
    protected static ?string $navigationLabel = 'Watermark Settings';
    protected static ?string $title = 'Watermark Settings';
    protected string $view = 'filament.pages.watermark-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = WatermarkSetting::firstOrCreate(
            ['tenant_id' => auth()->user()->tenant_id],
            ['enabled' => false, 'type' => 'text', 'position' => 'bottom-right', 'opacity' => 50, 'size' => 20]
        );
        $this->form->fill($settings->toArray());
    }

    protected function getFormSchema(): array
    {
        return [
            Toggle::make('enabled')->label('Enable Watermark'),
            Select::make('type')->options(['text' => 'Text', 'image' => 'Image'])->required(),
            TextInput::make('text')->label('Watermark Text'),
            Select::make('position')->options([
                'top-left' => 'Top Left',
                'top-center' => 'Top Center',
                'top-right' => 'Top Right',
                'center' => 'Center',
                'bottom-left' => 'Bottom Left',
                'bottom-center' => 'Bottom Center',
                'bottom-right' => 'Bottom Right',
                'tile' => 'Tile',
            ])->required(),
            TextInput::make('opacity')->numeric()->minValue(0)->maxValue(100)->suffix('%'),
            TextInput::make('size')->numeric()->minValue(1)->maxValue(100),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function save(): void
    {
        $data = $this->form->getState();
        WatermarkSetting::updateOrCreate(
            ['tenant_id' => auth()->user()->tenant_id],
            $data
        );
        \Filament\Notifications\Notification::make()->title('Settings saved')->success()->send();
    }
}
