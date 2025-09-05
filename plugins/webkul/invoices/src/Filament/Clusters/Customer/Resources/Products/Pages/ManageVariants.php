<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\ManageVariants as BaseManageVariants;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;
}
