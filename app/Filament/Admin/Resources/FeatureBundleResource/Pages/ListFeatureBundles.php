<?php

namespace App\Filament\Admin\Resources\FeatureBundleResource\Pages;

use App\Filament\Admin\Resources\FeatureBundleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeatureBundles extends ListRecords
{
    protected static string $resource = FeatureBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
