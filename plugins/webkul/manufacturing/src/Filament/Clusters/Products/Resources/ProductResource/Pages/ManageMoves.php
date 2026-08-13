<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = ProductResource::class;
}
