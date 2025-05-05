<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\CustomManageVariants as BaseManageVariants;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;
}
