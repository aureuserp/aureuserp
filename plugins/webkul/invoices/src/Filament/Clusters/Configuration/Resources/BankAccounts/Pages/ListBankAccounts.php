<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccounts\Pages;

use Webkul\Account\Filament\Resources\BankAccounts\Pages\ListBankAccounts as BaseManageBankAccounts;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccounts\BankAccountResource;

class ListBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = BankAccountResource::class;
}
