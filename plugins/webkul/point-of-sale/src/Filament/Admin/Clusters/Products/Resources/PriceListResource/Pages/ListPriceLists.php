<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages;

use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\ListPriceLists as BaseListPriceLists;

class ListPriceLists extends BaseListPriceLists
{
    protected static string $resource = PriceListResource::class;
}
