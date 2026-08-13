<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\CreateCustomer as BaseCreateCustomer;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;

class CreateCustomer extends BaseCreateCustomer
{
    protected static string $resource = CustomerResource::class;
}
