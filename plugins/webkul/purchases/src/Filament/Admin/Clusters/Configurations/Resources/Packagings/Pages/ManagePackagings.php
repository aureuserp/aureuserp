<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\Packagings\Pages;

use Webkul\Product\Filament\Resources\Packaging\Pages\ManagePackagings as BaseManagePackagings;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\Packagings\PackagingResource;

class ManagePackagings extends BaseManagePackagings
{
    protected static string $resource = PackagingResource::class;
}
