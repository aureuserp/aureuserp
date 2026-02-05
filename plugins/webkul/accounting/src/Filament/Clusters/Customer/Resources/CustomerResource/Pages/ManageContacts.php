<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = CustomerResource::class;
}
