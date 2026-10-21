<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\CreatePriceList as BaseCreatePriceList;

class CreatePriceList extends BaseCreatePriceList
{
    protected static string $resource = PriceListResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
