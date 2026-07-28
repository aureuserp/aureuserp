<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = ProductResource::class;
}
