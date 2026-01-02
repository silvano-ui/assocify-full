<?php

namespace Modules\Reports\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;

class FiscalReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-euro';

    protected string $view = 'reports::filament.pages.fiscal-reports';
    
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
                Select::make('type')
                    ->options([
                        'registro_iva' => 'Registro IVA',
                        'prima_nota' => 'Prima Nota',
                        'riepilogo_quote' => 'Riepilogo Quote',
                    ])
                    ->required(),
                Select::make('year')
                    ->options(array_combine(range(date('Y'), date('Y') - 5), range(date('Y'), date('Y') - 5)))
                    ->required(),
                Select::make('month')
                    ->options([
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                    ]),
            ])
            ->statePath('data');
    }

    public function generate(): void
    {
        // Logic to generate fiscal report
    }
}
