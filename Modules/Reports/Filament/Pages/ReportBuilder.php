<?php

namespace Modules\Reports\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;

class ReportBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected string $view = 'reports::filament.pages.report-builder';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('data_source')
                    ->options(config('reports.data_sources', []))
                    ->live()
                    ->required(),
                CheckboxList::make('columns')
                    ->options([
                        'id' => 'ID',
                        'name' => 'Name',
                        'email' => 'Email',
                        'created_at' => 'Created At',
                    ]),
                Repeater::make('filters')
                    ->schema([
                        Select::make('column'),
                        Select::make('operator')->options(['=' => '=', '>' => '>', '<' => '<', 'like' => 'Like']),
                        TextInput::make('value'),
                    ]),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        // Logic to generate report
    }
}
