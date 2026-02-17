<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ViewVendor as BaseViewVendor;

class ViewVendor extends BaseViewVendor
{
    protected static string $resource = VendorResource::class;
}
