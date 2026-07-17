<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageVendors as BaseManageVendors;

class ManageVendors extends BaseManageVendors
{
    protected static string $resource = ProductResource::class;
}
