<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageVariants as BaseManageVariants;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;
}
