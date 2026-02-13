<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Account\Filament\Resources\VendorResource\Pages\EditVendor as BaseEditVendor;

class EditVendor extends BaseEditVendor
{
    protected static string $resource = VendorResource::class;
}
