<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ListVendors as BaseListVendors;

class ListVendors extends BaseListVendors
{
    protected static string $resource = VendorResource::class;
}
