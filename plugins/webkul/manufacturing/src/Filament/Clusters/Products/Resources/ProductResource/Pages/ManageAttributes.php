<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;
}
