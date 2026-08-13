<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\ListVendors as BaseListVendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;

class ListVendors extends BaseListVendors
{
    protected static string $resource = VendorResource::class;
}
