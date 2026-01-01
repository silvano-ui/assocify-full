<?php

namespace Modules\Members\Filament\Resources\MemberCategoryResource\Pages;

use Modules\Members\Filament\Resources\MemberCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberCategories extends ListRecords
{
    protected static string $resource = MemberCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
