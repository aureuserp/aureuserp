<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'attributes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';
}
