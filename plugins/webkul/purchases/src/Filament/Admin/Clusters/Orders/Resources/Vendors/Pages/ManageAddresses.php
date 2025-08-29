<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Vendors\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\VendorResource;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = VendorResource::class;
}
