<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages;

use Webkul\Account\Filament\Resources\PaymentTerms\Pages\CreatePaymentTerm as BaseCreatePaymentTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\PaymentTermResource;

class CreatePaymentTerm extends BaseCreatePaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
