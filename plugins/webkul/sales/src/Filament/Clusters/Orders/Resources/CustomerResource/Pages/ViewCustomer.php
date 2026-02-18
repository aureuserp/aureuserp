<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ViewCustomer as BaseViewCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

class ViewCustomer extends BaseViewCustomer
{
    protected static string $resource = CustomerResource::class;
}
