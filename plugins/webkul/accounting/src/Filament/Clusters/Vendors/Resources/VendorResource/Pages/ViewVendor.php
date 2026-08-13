<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\ViewVendor as BaseViewVendor;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;

class ViewVendor extends BaseViewVendor
{
    protected static string $resource = VendorResource::class;
}
