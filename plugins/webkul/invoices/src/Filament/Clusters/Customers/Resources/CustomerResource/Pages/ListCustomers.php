<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ListCustomers as BaseListCustomers;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource;

class ListCustomers extends BaseListCustomers
{
    protected static string $resource = CustomerResource::class;
}
