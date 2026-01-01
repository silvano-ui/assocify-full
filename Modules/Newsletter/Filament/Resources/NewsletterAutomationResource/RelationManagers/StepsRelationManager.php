<?php

namespace Modules\Newsletter\Filament\Resources\NewsletterAutomationResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Modules\Newsletter\Entities\NewsletterTemplate;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('step_order')
                    ->numeric()
                    ->required(),
                Select::make('action_type')
                    ->options([
                        'send_email' => 'Send Email',
                        'wait' => 'Wait',
                        'condition' => 'Condition',
                        'update_subscriber' => 'Update Subscriber',
                    ])
                    ->required()
                    ->reactive(),
                Select::make('template_id')
                    ->label('Template')
                    ->options(NewsletterTemplate::pluck('name', 'id'))
                    ->visible(fn (callable $get) => $get('action_type') === 'send_email'),
                TextInput::make('email_subject')
                    ->visible(fn (callable $get) => $get('action_type') === 'send_email'),
                TextInput::make('wait_duration')
                    ->numeric()
                    ->visible(fn (callable $get) => $get('action_type') === 'wait'),
                Select::make('wait_unit')
                    ->options([
                        'minutes' => 'Minutes',
                        'hours' => 'Hours',
                        'days' => 'Days',
                    ])
                    ->visible(fn (callable $get) => $get('action_type') === 'wait'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('step_order')->sortable(),
                Tables\Columns\TextColumn::make('action_type')->badge(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
