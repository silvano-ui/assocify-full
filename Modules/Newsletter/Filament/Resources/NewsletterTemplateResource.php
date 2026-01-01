<?php

namespace Modules\Newsletter\Filament\Resources;

use Modules\Newsletter\Filament\Resources\NewsletterTemplateResource\Pages;
use Modules\Newsletter\Entities\NewsletterTemplate;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;

class NewsletterTemplateResource extends Resource
{
    protected static ?string $model = NewsletterTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string | \UnitEnum | null $navigationGroup = 'Newsletter';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options([
                        'drag_drop' => 'Drag & Drop',
                        'html' => 'HTML',
                        'mjml' => 'MJML',
                    ])
                    ->required(),
                TextInput::make('category')
                    ->maxLength(255),
                FileUpload::make('thumbnail_path')
                    ->image()
                    ->directory('newsletter-templates'),
                RichEditor::make('html_content')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label('Thumbnail'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('usage_count')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (NewsletterTemplate $record) => '#') // Placeholder for preview URL
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterTemplates::route('/'),
            'create' => Pages\CreateNewsletterTemplate::route('/create'),
            'edit' => Pages\EditNewsletterTemplate::route('/{record}/edit'),
        ];
    }
}
