<?php

namespace Webkul\Account\Filament\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ManageVariants as BaseManageVariants;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;
}
