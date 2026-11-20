<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = CustomerResource::class;
}
