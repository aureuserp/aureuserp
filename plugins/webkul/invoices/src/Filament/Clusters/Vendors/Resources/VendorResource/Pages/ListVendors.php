<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\ListVendors as BaseListVendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;

class ListVendors extends BaseListVendors
{
    protected static string $resource = VendorResource::class;
}
