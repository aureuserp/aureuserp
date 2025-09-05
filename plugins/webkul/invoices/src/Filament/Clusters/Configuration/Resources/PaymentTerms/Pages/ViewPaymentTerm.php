<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\Pages;

use Webkul\Account\Filament\Resources\PaymentTerms\Pages\ViewPaymentTerm as BaseViewPaymentTerm;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTerms\PaymentTermResource;

class ViewPaymentTerm extends BaseViewPaymentTerm
{
    protected static string $resource = PaymentTermResource::class;
}
