<?php

namespace App\Filament\Dashboard\Resources\ApiSecurityEvents\Schemas;

use Filament\Schemas\Schema;

class ApiSecurityEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
