<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageContacts as BaseManageContacts;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = CustomerResource::class;
}
