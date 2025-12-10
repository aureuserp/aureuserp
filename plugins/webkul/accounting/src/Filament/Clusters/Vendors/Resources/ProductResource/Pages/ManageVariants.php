<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageVariants as BaseManageVariants;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource;

class ManageVariants extends BaseManageVariants
{
    protected static string $resource = ProductResource::class;
}
