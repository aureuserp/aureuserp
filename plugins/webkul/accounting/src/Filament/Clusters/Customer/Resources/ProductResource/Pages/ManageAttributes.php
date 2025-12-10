<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;
}
