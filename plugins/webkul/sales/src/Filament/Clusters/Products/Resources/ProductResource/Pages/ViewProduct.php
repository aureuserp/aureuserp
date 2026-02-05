<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Product\Filament\Resources\ProductResource\Pages\ViewProduct as BaseViewProduct;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;
}
