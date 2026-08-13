<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = CustomerResource::class;
}
