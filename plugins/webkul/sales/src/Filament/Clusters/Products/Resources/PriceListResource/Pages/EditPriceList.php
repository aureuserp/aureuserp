<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages;

use Webkul\Product\Filament\Resources\PriceListResource\Pages\EditPriceList as BaseEditPriceList;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource;

class EditPriceList extends BaseEditPriceList
{
    protected static string $resource = PriceListResource::class;
}
