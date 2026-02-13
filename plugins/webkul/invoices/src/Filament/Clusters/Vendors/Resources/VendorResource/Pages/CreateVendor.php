<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Account\Filament\Resources\VendorResource\Pages\CreateVendor as BaseCreateVendor;

class CreateVendor extends BaseCreateVendor
{
    protected static string $resource = VendorResource::class;
}
