<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts as BaseListProducts;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
