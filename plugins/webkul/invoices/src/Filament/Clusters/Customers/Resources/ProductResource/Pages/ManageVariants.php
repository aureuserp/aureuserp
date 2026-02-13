<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageVariants as BaseManageVariants;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;
}
