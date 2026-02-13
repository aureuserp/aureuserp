<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ListCustomers as BaseListCustomers;

class ListCustomers extends BaseListCustomers
{
    protected static string $resource = CustomerResource::class;
}
