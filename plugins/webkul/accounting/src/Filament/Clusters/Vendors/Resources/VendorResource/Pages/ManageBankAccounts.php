<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = VendorResource::class;
}
