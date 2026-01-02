<?php

namespace Modules\Localization\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;

class TranslationCoverageWidget extends BaseWidget
{
    protected static ?int $sort = 14;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Language::query()->where('is_active', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Language'),
                Tables\Columns\TextColumn::make('stats')
                    ->label('Coverage')
                    ->getStateUsing(function (Language $record) {
                        // Total unique keys in the system (across all languages? or just base?)
                        // Usually we take the 'default' language or 'en' as the source of truth.
                        // Or count distinct group+key.
                        $totalKeys = Translation::distinct()->count('key'); // Simplified. Should be distinct group+key.
                        // Let's do distinct group+key
                        $totalKeys = Translation::select('group', 'key')->distinct()->get()->count();
                        
                        if ($totalKeys === 0) return 'N/A';

                        $translated = Translation::where('locale', $record->code)->count();
                        $percentage = round(($translated / $totalKeys) * 100, 1);
                        
                        return "{$translated} / {$totalKeys} ({$percentage}%)";
                    }),
                Tables\Columns\TextColumn::make('missing')
                    ->label('Missing')
                    ->getStateUsing(function (Language $record) {
                        $totalKeys = Translation::select('group', 'key')->distinct()->get()->count();
                        $translated = Translation::where('locale', $record->code)->count();
                        return $totalKeys - $translated;
                    }),
            ]);
    }
}
