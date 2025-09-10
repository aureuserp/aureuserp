<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\EditVendor as BaseEditVendor;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\VendorResource;

class EditVendor extends BaseEditVendor
{
    protected static string $resource = VendorResource::class;
}
