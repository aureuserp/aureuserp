<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\ViewVendor as BaseViewVendor;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\VendorResource;

class ViewVendor extends BaseViewVendor
{
    protected static string $resource = VendorResource::class;
}
