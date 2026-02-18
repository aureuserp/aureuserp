<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\CreateCustomer as BaseCreateCustomer;

class CreateCustomer extends BaseCreateCustomer
{
    protected static string $resource = CustomerResource::class;
}
