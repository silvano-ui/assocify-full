<?php

namespace Modules\Localization\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Filament\Resources\TranslationResource\Pages;
use Modules\Localization\Services\AutoTranslateService;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Collection;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-language';

    protected static string | \UnitEnum | null $navigationGroup = 'Localization';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('locale')
                    ->options(fn () => \Modules\Localization\Entities\Language::pluck('name', 'code'))
                    ->required(),
                Forms\Components\TextInput::make('group')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('value')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_auto_translated')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('locale')
                    ->options(fn () => \Modules\Localization\Entities\Language::pluck('name', 'code')),
                Tables\Filters\SelectFilter::make('group')
                    ->options(fn () => Translation::distinct()->pluck('group', 'group')),
                Tables\Filters\TernaryFilter::make('is_auto_translated'),
                Tables\Filters\Filter::make('unreviewed')
                    ->query(fn (Builder $query) => $query->whereNull('reviewed_at')),
            ])
            ->actions([
                EditAction::make(),
                Action::make('auto_translate')
                    ->label('Auto Translate')
                    ->icon('heroicon-o-sparkles')
                    ->action(function (Translation $record) {
                        $service = app(AutoTranslateService::class);
                        // Assuming translate method exists and handles single translation update
                        // We might need to call a specific method or just translate the text and update
                        // The service interface from Part 4 was: translate(string $text, string $targetLocale, string $sourceLocale = null, ?string $provider = null): ?string
                        
                        // We need source text. Usually we translate from a base language (e.g., 'en' or platform default).
                        // If this record IS the base language, we can't auto-translate it into itself.
                        // But usually we want to translate *into* this record's locale from a default locale.
                        
                        // Let's assume we find the default locale translation for this group/key.
                        $defaultLocale = config('app.locale', 'en'); // Or get from LanguageService
                        if ($record->locale === $defaultLocale) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot auto-translate default locale')
                                ->warning()
                                ->send();
                            return;
                        }

                        $sourceTranslation = Translation::where('locale', $defaultLocale)
                            ->where('group', $record->group)
                            ->where('key', $record->key)
                            ->first();

                        if (!$sourceTranslation || empty($sourceTranslation->value)) {
                             \Filament\Notifications\Notification::make()
                                ->title('Source translation not found')
                                ->warning()
                                ->send();
                            return;
                        }

                        $translatedText = $service->translate($sourceTranslation->value, $record->locale, $defaultLocale);
                        
                        if ($translatedText) {
                            $record->update([
                                'value' => $translatedText,
                                'is_auto_translated' => true,
                                'auto_translation_provider' => 'auto', // Or get from service
                            ]);
                             \Filament\Notifications\Notification::make()
                                ->title('Translation updated')
                                ->success()
                                ->send();
                        }
                    }),
                Action::make('mark_reviewed')
                    ->label('Mark Reviewed')
                    ->icon('heroicon-o-check')
                    ->action(function (Translation $record) {
                        $record->update([
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                        ]);
                    })
                    ->visible(fn (Translation $record) => $record->is_auto_translated || !$record->reviewed_at),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('mark_reviewed')
                        ->label('Mark Reviewed')
                        ->icon('heroicon-o-check')
                        ->action(function (Collection $records) {
                             $records->each(fn (Translation $record) => $record->update([
                                'reviewed_at' => now(),
                                'reviewed_by' => Auth::id(),
                            ]));
                        }),
                     // Auto-translate bulk would be complex, maybe skip for now or implement carefully
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
