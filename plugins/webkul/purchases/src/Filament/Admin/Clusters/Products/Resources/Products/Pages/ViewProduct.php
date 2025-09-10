<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\Pages;

use Webkul\Product\Filament\Resources\Products\Pages\ViewProduct as BaseViewProduct;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\ProductResource;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;
}
