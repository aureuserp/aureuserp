<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages;

use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\ViewPriceList as BaseViewPriceList;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPriceList extends BaseViewPriceList
{
    use HasRecordNavigationTabs;

    protected static string $resource = PriceListResource::class;
}
