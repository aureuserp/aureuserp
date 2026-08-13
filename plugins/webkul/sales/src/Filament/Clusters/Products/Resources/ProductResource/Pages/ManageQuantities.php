<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities as BaseManageQuantities;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;

class ManageQuantities extends BaseManageQuantities
{
    protected static string $resource = ProductResource::class;
}
