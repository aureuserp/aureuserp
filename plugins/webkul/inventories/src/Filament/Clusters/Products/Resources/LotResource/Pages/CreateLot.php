<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;

class CreateLot extends CreateRecord
{
    protected static string $resource = LotResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
