<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = CustomerResource::class;
}
