<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource;

class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = CustomerResource::class;
}
