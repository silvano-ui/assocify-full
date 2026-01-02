<?php

namespace Modules\Reports\Filament\Admin\Resources;

use Modules\Reports\Filament\Resources\ReportTemplateResource;
use Modules\Reports\Filament\Admin\Resources\AdminReportTemplateResource\Pages;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminReportTemplateResource extends ReportTemplateResource
{
    public static function getNavigationGroup(): ?string
    {
        return 'Reports & Analytics';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope('tenant');
    }

    public static function form(Schema $schema): Schema
    {
        $schema = parent::form($schema);
        
        return $schema->components([
            ...$schema->getComponents(),
            Toggle::make('is_system')
                ->label('System Template')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $table = parent::table($table);
        
        return $table->filters([
            SelectFilter::make('tenant')
                ->relationship('tenant', 'name')
                ->searchable()
                ->preload(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportTemplates::route('/'),
            'create' => Pages\CreateReportTemplate::route('/create'),
            'edit' => Pages\EditReportTemplate::route('/{record}/edit'),
        ];
    }
}
