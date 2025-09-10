<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\Packagings\Pages;

use Webkul\Product\Filament\Resources\Packaging\Pages\ManagePackagings as BaseManagePackagings;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\Packagings\PackagingResource;

class ManagePackagings extends BaseManagePackagings
{
    protected static string $resource = PackagingResource::class;
}
