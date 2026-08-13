<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\VendorResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = VendorResource::class;
}
