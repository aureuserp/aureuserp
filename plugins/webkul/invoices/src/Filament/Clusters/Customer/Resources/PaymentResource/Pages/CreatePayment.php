<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentResource\Pages;

use Webkul\Account\Filament\Resources\PaymentResource\Pages\CreatePayment as BaseCreatePayment;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentResource;

class CreatePayment extends BaseCreatePayment
{
    protected static string $resource = PaymentResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
