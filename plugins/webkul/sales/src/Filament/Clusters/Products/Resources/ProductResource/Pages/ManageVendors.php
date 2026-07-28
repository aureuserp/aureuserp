<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageVendors as BaseManageVendors;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;

class ManageVendors extends BaseManageVendors
{
    protected static string $resource = ProductResource::class;
}
