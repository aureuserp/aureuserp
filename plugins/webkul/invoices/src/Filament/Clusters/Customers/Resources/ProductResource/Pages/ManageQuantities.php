<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities as BaseManageQuantities;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource;

class ManageQuantities extends BaseManageQuantities
{
    protected static string $resource = ProductResource::class;
}
