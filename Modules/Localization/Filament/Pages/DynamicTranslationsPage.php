<?php

namespace Modules\Localization\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
use Modules\Localization\Services\DynamicTranslationService;

class DynamicTranslationsPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'localization::filament.pages.dynamic-translations-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'Dynamic Translations';

    public ?string $activeTab = 'Event'; // Default tab

    public function getTabs(): array
    {
        return [
            'Event' => 'Events',
            'Document' => 'Documents',
            'Newsletter' => 'Newsletters',
        ];
    }

    public function table(Table $table): Table
    {
        // This is tricky. We need to query different models based on active tab.
        // For simplicity, let's assume we have a way to get the query.
        // In a real app, we might need a polymorphic table or switching queries.
        
        // Let's assume we are listing DynamicTranslation records for now, 
        // OR we just show a placeholder if we can't easily dynamically switch the query source in one table definition.
        
        // BUT, Filament allows dynamic table query.
        
        return $table
            ->query(function () {
                $modelClass = $this->getModelClass($this->activeTab);
                if ($modelClass && class_exists($modelClass)) {
                    return $modelClass::query();
                }
                return \Modules\Localization\Entities\DynamicTranslation::query()->whereRaw('1=0'); // Empty
            })
            ->columns([
                TextColumn::make('title') // Assuming all have a title/name
                    ->label('Title')
                    ->searchable()
                    ->placeholder(fn ($record) => $record->name ?? $record->subject ?? 'N/A'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->actions([
                Action::make('translate')
                    ->label('Translate')
                    ->icon('heroicon-o-language')
                    ->modalContent(fn ($record) => view('localization::filament.pages.dynamic-translation-modal', ['record' => $record]))
                    ->action(function ($record, array $data) {
                        // Save translations
                        // This logic would be in the modal form submission or here if using a form schema
                    }),
            ]);
    }

    protected function getModelClass(string $type): ?string
    {
        // Map types to classes
        $map = [
            'Event' => 'Modules\Events\Entities\Event',
            'Document' => 'Modules\Documents\Entities\Document',
            'Newsletter' => 'Modules\Newsletters\Entities\Newsletter',
        ];
        return $map[$type] ?? null;
    }
}
