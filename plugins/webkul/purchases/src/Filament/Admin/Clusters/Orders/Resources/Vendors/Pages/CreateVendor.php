<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\CreateVendor as BaseCreateVendor;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\VendorResource;

class CreateVendor extends BaseCreateVendor
{
    protected static string $resource = VendorResource::class;
}
