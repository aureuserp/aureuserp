<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageVendors as BaseManageVendors;

class ManageVendors extends BaseManageVendors
{
    protected static string $resource = ProductResource::class;
}
