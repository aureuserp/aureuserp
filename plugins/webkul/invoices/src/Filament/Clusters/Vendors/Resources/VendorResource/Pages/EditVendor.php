<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\EditPartner as BaseEditVendor;

class EditVendor extends BaseEditVendor
{
    protected static string $resource = VendorResource::class;
}
