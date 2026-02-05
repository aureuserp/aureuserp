<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\AccountResource\Pages;

use Webkul\Account\Filament\Resources\AccountResource\Pages\ManageAccounts as BaseManageAccounts;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\AccountResource;

class ManageAccounts extends BaseManageAccounts
{
    protected static string $resource = AccountResource::class;
}
