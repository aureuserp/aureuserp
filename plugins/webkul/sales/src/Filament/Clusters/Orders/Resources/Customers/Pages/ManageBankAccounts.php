<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Customers\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Partners\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Customers\CustomerResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = CustomerResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
