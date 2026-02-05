<?php

namespace Webkul\Account\Filament\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;
}
