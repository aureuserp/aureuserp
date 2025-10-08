<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\AccountResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\AccountResource;
use Webkul\Account\Filament\Resources\AccountResource\Pages\ManageAccounts as BaseManageAccounts;

class ManageAccounts extends BaseManageAccounts
{
    protected static string $resource = AccountResource::class;
}
