<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ListProducts as BaseListProducts;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
