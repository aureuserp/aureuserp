<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageAddresses as BaseManageAddresses;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = CustomerResource::class;
}
