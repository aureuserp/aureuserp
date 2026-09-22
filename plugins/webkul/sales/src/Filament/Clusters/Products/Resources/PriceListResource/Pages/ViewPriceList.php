<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages;

use Webkul\Product\Filament\Resources\PriceListResource\Pages\ViewPriceList as BaseViewPriceList;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource;

class ViewPriceList extends BaseViewPriceList
{
    protected static string $resource = PriceListResource::class;
}
