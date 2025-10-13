<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ViewProduct as BaseViewProduct;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;
}
