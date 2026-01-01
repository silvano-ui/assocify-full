<?php

namespace App\Filament\Admin\Resources\PlanFeatureResource\Pages;

use App\Filament\Admin\Resources\PlanFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanFeatures extends ListRecords
{
    protected static string $resource = PlanFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
