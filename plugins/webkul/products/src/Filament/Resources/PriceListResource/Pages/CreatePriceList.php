<?php

namespace Webkul\Product\Filament\Resources\PriceListResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Product\Filament\Resources\PriceListResource;

class CreatePriceList extends CreateRecord
{
    protected static string $resource = PriceListResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
