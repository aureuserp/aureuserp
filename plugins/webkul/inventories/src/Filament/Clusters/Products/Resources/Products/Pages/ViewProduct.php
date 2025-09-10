<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Products\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\ViewProduct as BaseViewProduct;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;
}
