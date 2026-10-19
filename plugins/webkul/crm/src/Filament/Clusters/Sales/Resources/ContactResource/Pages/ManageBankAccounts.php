<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = ContactResource::class;
}
