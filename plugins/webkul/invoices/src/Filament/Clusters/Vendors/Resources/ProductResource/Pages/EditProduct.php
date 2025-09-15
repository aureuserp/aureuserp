<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\EditProduct as BaseEditProduct;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
