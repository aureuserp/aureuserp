<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\CreateCustomer as BaseCreateCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

class CreateCustomer extends BaseCreateCustomer
{
    protected static string $resource = CustomerResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('sales::filament/clusters/orders/resources/customer/pages/create-customer.title');
    }
}
