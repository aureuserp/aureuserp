<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageQuantities as BaseManageQuantities;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageQuantities extends BaseManageQuantities
{
    protected static string $resource = ProductResource::class;
}
