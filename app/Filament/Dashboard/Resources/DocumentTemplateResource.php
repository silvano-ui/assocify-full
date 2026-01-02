<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\DocumentTemplateResource\Pages;
use Modules\Documents\Entities\DocumentTemplate;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;
use BackedEnum;

class DocumentTemplateResource extends Resource
{
    protected static ?string $model = DocumentTemplate::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';
    
    protected static string | UnitEnum | null $navigationGroup = 'Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('type')
                    ->options([
                        'pdf_fillable' => 'PDF Fillable',
                        'html_to_pdf' => 'HTML to PDF',
                        'docx_merge' => 'DOCX Merge',
                        'certificate' => 'Certificate',
                        'receipt' => 'Receipt',
                        'membership_card' => 'Membership Card',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535),
                    
                Forms\Components\FileUpload::make('base_file_path')
                    ->label('Base Template File')
                    ->visible(fn (Get $get) => in_array($get('type'), ['pdf_fillable', 'docx_merge']))
                    ->directory('templates'),
                    
                Forms\Components\RichEditor::make('html_content')
                    ->label('HTML Content')
                    ->visible(fn (Get $get) => in_array($get('type'), ['html_to_pdf', 'certificate', 'receipt', 'membership_card'])),
                
                Forms\Components\Textarea::make('css_styles')
                    ->label('CSS Styles')
                    ->visible(fn (Get $get) => in_array($get('type'), ['html_to_pdf', 'certificate', 'receipt', 'membership_card'])),
                    
                Forms\Components\Select::make('output_format')
                    ->options(['pdf' => 'PDF', 'docx' => 'DOCX', 'png' => 'PNG'])
                    ->default('pdf')
                    ->required(),
                Forms\Components\TextInput::make('page_size')
                    ->default('A4'),
                Forms\Components\Select::make('orientation')
                    ->options(['portrait' => 'Portrait', 'landscape' => 'Landscape'])
                    ->default('portrait'),
                    
                Forms\Components\KeyValue::make('available_variables')
                    ->keyLabel('Variable Name')
                    ->valueLabel('Description'),
                    
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'pdf_fillable' => 'PDF Fillable',
                        'html_to_pdf' => 'HTML to PDF',
                        'docx_merge' => 'DOCX Merge',
                        'certificate' => 'Certificate',
                        'receipt' => 'Receipt',
                        'membership_card' => 'Membership Card',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDocumentTemplates::route('/'),
            'create' => Pages\CreateDocumentTemplate::route('/create'),
            'edit' => Pages\EditDocumentTemplate::route('/{record}/edit'),
        ];
    }
}
