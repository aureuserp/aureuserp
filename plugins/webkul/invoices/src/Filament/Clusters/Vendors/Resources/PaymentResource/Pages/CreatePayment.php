<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages;

use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\CreatePayment as BaseCreatePayment;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource;

class CreatePayment extends BaseCreatePayment
{
    protected static string $resource = PaymentResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->data['payment_type'] ??= PaymentType::SEND;

        $this->form->fill($this->data);
    }
}
