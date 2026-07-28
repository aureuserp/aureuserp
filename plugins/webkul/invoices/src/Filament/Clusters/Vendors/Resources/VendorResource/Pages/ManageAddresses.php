<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = VendorResource::class;
}
