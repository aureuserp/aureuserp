<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\Pages;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\ManageAttributes as BaseManageAttributes;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'attributes';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
