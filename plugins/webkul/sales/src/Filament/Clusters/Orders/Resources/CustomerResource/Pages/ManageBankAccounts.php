<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageBankAccounts extends BaseManageBankAccounts
{
    use HasRecordNavigationTabs;

    protected static string $resource = CustomerResource::class;
}
