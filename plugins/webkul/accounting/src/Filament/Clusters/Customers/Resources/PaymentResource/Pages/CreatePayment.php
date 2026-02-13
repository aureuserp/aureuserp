<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages;

use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\CreatePayment as BaseCreatePayment;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource;

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

    public function mount(): void
    {
        parent::mount();

        $this->data['payment_type'] ??= PaymentType::RECEIVE;

        $this->form->fill($this->data);
    }
}
