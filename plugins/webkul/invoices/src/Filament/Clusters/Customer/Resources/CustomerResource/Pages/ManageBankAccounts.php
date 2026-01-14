<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageBankAccounts as BaseManageBankAccounts;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = CustomerResource::class;
}
