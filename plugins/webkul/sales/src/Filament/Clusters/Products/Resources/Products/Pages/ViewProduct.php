<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\Products\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Product\Filament\Resources\Products\Pages\ViewProduct as BaseViewProduct;
use Webkul\Sale\Filament\Clusters\Products\Resources\Products\ProductResource;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
