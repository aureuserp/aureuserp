<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages;

use Webkul\Product\Filament\Resources\PriceListResource\Pages\ListPriceLists as BaseListPriceLists;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource;

class ListPriceLists extends BaseListPriceLists
{
    protected static string $resource = PriceListResource::class;
}
