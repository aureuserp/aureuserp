<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Packagings\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Packagings\PackagingResource;
use Webkul\Product\Filament\Resources\Packaging\Pages\ManagePackagings as BaseManagePackagings;

class ManagePackagings extends BaseManagePackagings
{
    protected static string $resource = PackagingResource::class;
}
