<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccounts\Pages;

use Webkul\Contact\Filament\Clusters\Configurations\Resources\BankAccounts\BankAccountResource;
use Webkul\Partner\Filament\Resources\BankAccounts\Pages\ManageBankAccounts as BaseManageBankAccounts;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = BankAccountResource::class;
}
