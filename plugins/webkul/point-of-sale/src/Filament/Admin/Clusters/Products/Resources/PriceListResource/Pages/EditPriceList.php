<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages;

use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\EditPriceList as BaseEditPriceList;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPriceList extends BaseEditPriceList
{
    use HasRecordNavigationTabs;

    protected static string $resource = PriceListResource::class;
}
