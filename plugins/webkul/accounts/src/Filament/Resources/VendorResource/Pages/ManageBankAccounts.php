<?php

namespace Webkul\Account\Filament\Resources\VendorResource\Pages;

use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Account\Filament\Resources\VendorResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = VendorResource::class;
}
