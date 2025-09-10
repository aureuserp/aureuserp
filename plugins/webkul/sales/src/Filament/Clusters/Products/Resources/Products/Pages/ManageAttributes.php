<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\Sale\Filament\Clusters\Products\Resources\Products\ProductResource;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;
}
