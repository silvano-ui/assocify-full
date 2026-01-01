<?php

namespace Modules\Gallery\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms;
use Modules\Gallery\Entities\WatermarkSetting;

class WatermarkSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gallery';
    }

    protected static ?string $title = 'Watermark Settings';

    protected string $view = 'gallery::filament.pages.watermark-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = WatermarkSetting::first();
        
        if (!$settings) {
            // Defaults
            $this->form->fill([
                'enabled' => false,
                'type' => 'text',
                'position' => 'bottom-right',
                'opacity' => 50,
                'size' => 20,
            ]);
        } else {
            $this->form->fill($settings->attributesToArray());
        }
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\Toggle::make('enabled')
                    ->label('Enable Watermarking'),
                
                Forms\Components\Select::make('type')
                    ->options([
                        'text' => 'Text',
                        'image' => 'Image',
                    ])
                    ->reactive()
                    ->required(),

                Forms\Components\TextInput::make('text')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'text')
                    ->required(fn (Forms\Get $get) => $get('type') === 'text'),

                Forms\Components\FileUpload::make('image_path')
                    ->label('Watermark Image')
                    ->disk('public')
                    ->directory('watermarks')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                    ->required(fn (Forms\Get $get) => $get('type') === 'image'),

                Forms\Components\Select::make('position')
                    ->options([
                        'top-left' => 'Top Left',
                        'top-center' => 'Top Center',
                        'top-right' => 'Top Right',
                        'center' => 'Center',
                        'bottom-left' => 'Bottom Left',
                        'bottom-center' => 'Bottom Center',
                        'bottom-right' => 'Bottom Right',
                        'tile' => 'Tile',
                    ])
                    ->required(),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('opacity')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(50),
                        Forms\Components\TextInput::make('size')
                            ->label('Size (Percentage of target)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(20),
                    ]),
            ]);
    }

    public function save(): void
    {
        $settings = WatermarkSetting::first();
        if (!$settings) {
            $settings = new WatermarkSetting();
        }
        
        $settings->fill($this->form->getState());
        $settings->save();

        Notification::make() 
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
