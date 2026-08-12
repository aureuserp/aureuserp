<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource\Schemas;

use Filament\Schemas\Schema;

class ReplenishmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            ])
            ->columns(1);
    }
}
