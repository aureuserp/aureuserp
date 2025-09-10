<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\ManageVariants as BaseManageVariants;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
