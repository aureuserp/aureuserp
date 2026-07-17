<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = ProductResource::class;
}
