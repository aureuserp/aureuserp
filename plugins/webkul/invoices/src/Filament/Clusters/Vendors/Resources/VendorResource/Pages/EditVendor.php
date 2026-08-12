<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\EditVendor as BaseEditVendor;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;

class EditVendor extends BaseEditVendor
{
    protected static string $resource = VendorResource::class;
}
