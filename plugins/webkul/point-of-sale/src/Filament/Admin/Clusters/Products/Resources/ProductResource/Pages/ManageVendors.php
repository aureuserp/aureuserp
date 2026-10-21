<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageVendors as BaseManageVendors;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageVendors extends BaseManageVendors
{
    protected static string $resource = ProductResource::class;
}
