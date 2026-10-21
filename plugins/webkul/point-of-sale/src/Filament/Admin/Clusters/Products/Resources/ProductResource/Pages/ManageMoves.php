<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = ProductResource::class;
}
