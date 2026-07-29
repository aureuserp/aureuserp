<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Contact\Filament\Resources\PartnerResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = PartnerResource::class;
}
