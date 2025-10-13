<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'attributes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';
}
