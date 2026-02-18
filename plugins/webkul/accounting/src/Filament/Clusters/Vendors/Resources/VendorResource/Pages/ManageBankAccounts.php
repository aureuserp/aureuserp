<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageBankAccounts as BaseManageBankAccounts;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = VendorResource::class;
}
