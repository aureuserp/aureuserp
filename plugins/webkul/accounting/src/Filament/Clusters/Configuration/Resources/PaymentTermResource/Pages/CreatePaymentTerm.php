<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\CreatePaymentTerm as BaseCreatePaymentTerm;

class CreatePaymentTerm extends BaseCreatePaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
