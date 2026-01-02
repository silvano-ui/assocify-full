<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Todo;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;

class TodoWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'To Do List / Notes';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Todo::query()
                    ->where('user_id', Auth::id())
                    ->latest()
            )
            ->columns([
                Tables\Columns\CheckboxColumn::make('is_completed')
                    ->label('Done')
                    ->alignCenter()
                    ->width('50px'),
                Tables\Columns\TextColumn::make('content')
                    ->label('Task / Note')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('attachments')
                    ->label('Attachments')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->width('100px'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Add Task')
                    ->model(Todo::class)
                    ->form([
                        Forms\Components\Textarea::make('content')
                            ->label('Task / Note')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Attachments / Photos')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->directory('todo-attachments'),
                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Due Date'),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->form([
                        Forms\Components\Textarea::make('content')
                            ->label('Task / Note')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Attachments / Photos')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->directory('todo-attachments'),
                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Due Date'),
                    ]),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->filters([
                Tables\Filters\Filter::make('hide_completed')
                    ->label('Nascondi Completati')
                    ->query(fn ($query) => $query->where('is_completed', false))
                    ->default(),
            ])
            ->paginated(false);
    }
}
