<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVariants as BaseManageVariants;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;
}
