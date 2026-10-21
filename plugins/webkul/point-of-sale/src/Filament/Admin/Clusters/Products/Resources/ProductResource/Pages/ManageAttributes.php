<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;
}
