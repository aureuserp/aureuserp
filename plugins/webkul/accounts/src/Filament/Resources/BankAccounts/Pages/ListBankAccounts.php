<?php

namespace Webkul\Account\Filament\Resources\BankAccounts\Pages;

use Webkul\Account\Filament\Resources\BankAccounts\BankAccountResource;
use Webkul\Partner\Filament\Resources\BankAccounts\Pages\ManageBankAccounts as BaseManageBankAccounts;

class ListBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = BankAccountResource::class;
}
