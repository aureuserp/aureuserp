<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\EditCustomer as BaseEditCustomer;

class EditCustomer extends BaseEditCustomer
{
    protected static string $resource = CustomerResource::class;
}
