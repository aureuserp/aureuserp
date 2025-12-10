<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource\Pages;

use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\CreatePaymentTerm as BaseCreatePaymentTerm;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource;

class CreatePaymentTerm extends BaseCreatePaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
